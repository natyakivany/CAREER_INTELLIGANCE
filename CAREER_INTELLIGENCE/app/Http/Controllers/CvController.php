<?php

namespace App\Http\Controllers;

use App\Models\Occupation;
use App\Models\StudentProfile;
use App\Services\GeminiAssessmentService;
use App\Services\SkillGapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CvController extends Controller
{
    public function __construct(
        protected GeminiAssessmentService $geminiService,
        protected SkillGapService $skillGapService,
    ) {}

    /**
     * Display ATS CV Builder page.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = StudentProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'prodi' => 'Teknik Komputer',
                'jurusan' => 'Teknologi Informasi',
                'semester' => 'Semester 7',
                'target_field' => 'it',
            ]
        );

        $portfolios = $user->portfolios()->latest()->get();
        $latestAssessment = $user->assessments()->where('status', 'completed')->latest()->first();

        // Target occupation
        $targetOccupation = $profile->targetOccupation
            ?? Occupation::where('field', $profile->target_field ?? 'it')->first()
            ?? Occupation::first();

        $isCvSaved = $profile->isCvFilled();

        // If CV summary / headline is not set yet, provide smart initial content in memory for form suggestions
        if (! $isCvSaved) {
            $initialContent = $this->geminiService->generateCvContent($user, $targetOccupation);
            if (empty($profile->cv_headline)) {
                $profile->cv_headline = $initialContent['headline'];
            }
            if (empty($profile->cv_summary)) {
                $profile->cv_summary = $initialContent['summary'];
            }
            if (empty($profile->cv_custom_skills) && ! empty($initialContent['skills_keywords'])) {
                $profile->cv_custom_skills = $initialContent['skills_keywords'];
            }
            // Do not call $profile->save() here so the CV remains inactive until the student explicitly saves or optimizes it.
        }

        // Calculate completeness
        $completeness = 20; // Basic account
        if ($profile->phone) {
            $completeness += 15;
        }
        if ($profile->cv_headline) {
            $completeness += 15;
        }
        if ($profile->cv_summary) {
            $completeness += 20;
        }
        if ($latestAssessment) {
            $completeness += 15;
        }
        if ($portfolios->isNotEmpty()) {
            $completeness += 15;
        }
        $completeness = min(100, $completeness);

        $employabilityScore = $this->skillGapService->calculateEmployabilityScore($user, $latestAssessment);

        return view('cv-builder', [
            'user' => $user,
            'profile' => $profile,
            'portfolios' => $portfolios,
            'latestAssessment' => $latestAssessment,
            'targetOccupation' => $targetOccupation,
            'completeness' => $completeness,
            'employabilityScore' => $employabilityScore,
        ]);
    }

    /**
     * Update CV customization fields.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        // Auto-prepend https:// jika pengguna memasukkan URL tanpa protokol (seperti www.linkedin.com/...)
        foreach (['linkedin_url', 'github_url'] as $urlField) {
            if ($request->filled($urlField)) {
                $val = trim($request->input($urlField));
                if (! preg_match('~^https?://~i', $val)) {
                    $request->merge([$urlField => 'https://'.$val]);
                }
            }
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'cv_headline' => ['nullable', 'string', 'max:255'],
            'cv_summary' => ['nullable', 'string', 'max:2500'],
            'cv_custom_skills' => ['nullable', 'string', 'max:1000'],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'prodi' => ['nullable', 'string', 'max:255'],
            'high_school_name' => ['nullable', 'string', 'max:255'],
            'high_school_major' => ['nullable', 'string', 'max:255'],
            'high_school_year' => ['nullable', 'string', 'max:50'],
            'cv_internships' => ['nullable', 'string', 'max:5000'],
            'cv_organizations' => ['nullable', 'string', 'max:5000'],
            'cv_tools' => ['nullable', 'string', 'max:5000'],
            'cv_certificates' => ['nullable', 'string', 'max:5000'],
        ]);

        $user->update(['name' => $validated['name']]);

        $skillsArray = [];
        if (! empty($validated['cv_custom_skills'])) {
            $skillsArray = array_values(array_filter(array_map('trim', explode(',', $validated['cv_custom_skills']))));
        }

        if ($profile) {
            $profile->update([
                'phone' => $validated['phone'] ?? $profile->phone,
                'address' => $validated['address'] ?? $profile->address,
                'linkedin_url' => $validated['linkedin_url'] ?? $profile->linkedin_url,
                'github_url' => $validated['github_url'] ?? $profile->github_url,
                'cv_headline' => $validated['cv_headline'] ?? $profile->cv_headline,
                'cv_summary' => $validated['cv_summary'] ?? $profile->cv_summary,
                'cv_custom_skills' => ! empty($skillsArray) ? $skillsArray : $profile->cv_custom_skills,
                'institution_name' => $validated['institution_name'] ?? $profile->institution_name,
                'jurusan' => $validated['jurusan'] ?? $profile->jurusan,
                'prodi' => $validated['prodi'] ?? $profile->prodi,
                'high_school_name' => $validated['high_school_name'] ?? $profile->high_school_name,
                'high_school_major' => $validated['high_school_major'] ?? $profile->high_school_major,
                'high_school_year' => $validated['high_school_year'] ?? $profile->high_school_year,
                'cv_internships' => $validated['cv_internships'] ?? $profile->cv_internships,
                'cv_organizations' => $validated['cv_organizations'] ?? $profile->cv_organizations,
                'cv_tools' => $validated['cv_tools'] ?? $profile->cv_tools,
                'cv_certificates' => $validated['cv_certificates'] ?? $profile->cv_certificates,
            ]);
        }

        return redirect()->route('cv-builder')->with('status', 'Data CV ATS berhasil diperbarui dan disimpan!');
    }

    /**
     * Re-generate professional summary and keywords with Gemini AI.
     */
    public function generateAi(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->studentProfile;
        $targetOccupation = $profile?->targetOccupation
            ?? Occupation::where('field', $profile?->target_field === 'all' ? 'it' : ($profile?->target_field ?? 'it'))->first()
            ?? Occupation::first();

        $aiContent = $this->geminiService->generateCvContent($user, $targetOccupation);

        if ($profile) {
            $profile->update([
                'cv_headline' => $aiContent['headline'],
                'cv_summary' => $aiContent['summary'],
                'cv_custom_skills' => $aiContent['skills_keywords'] ?: $profile->cv_custom_skills,
            ]);
        }

        return redirect()->route('cv-builder')->with('status', 'Ringkasan CV berhasil dioptimasi dengan Gemini AI!');
    }

    /**
     * Render full-page clean print view for direct A4 PDF export.
     */
    public function print(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        if (! $profile?->isCvFilled()) {
            return redirect()->route('cv-builder')
                ->with('error', 'Silakan lengkapi atau simpan formulir CV ATS Anda terlebih dahulu agar CV aktif.');
        }

        $portfolios = $user->portfolios()->latest()->get();
        $latestAssessment = $user->assessments()->where('status', 'completed')->latest()->first();
        $targetOccupation = $profile?->targetOccupation ?? Occupation::first();

        return view('cv-print', [
            'user' => $user,
            'profile' => $profile,
            'portfolios' => $portfolios,
            'latestAssessment' => $latestAssessment,
            'targetOccupation' => $targetOccupation,
        ]);
    }
}
