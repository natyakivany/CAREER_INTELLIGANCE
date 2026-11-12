<?php

namespace App\Http\Controllers;

use App\Mail\MitraNotificationMail;
use App\Models\CareerRoadmap;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Occupation;
use App\Models\SkillGapResult;
use App\Models\StudentProfile;
use App\Notifications\CandidateResponseNotification;
use App\Notifications\NewJobApplicationNotification;
use App\Services\SkillGapService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function __construct(
        protected SkillGapService $skillGapService,
    ) {}

    /**
     * Display student main dashboard with real computed metrics.
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->role === 'admin' && ! $request->boolean('preview')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'mitra') {
            return redirect()->route('mitra.dashboard');
        }

        $profile = StudentProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'prodi' => 'Teknik Komputer',
                'jurusan' => 'Teknologi Informasi',
                'semester' => 'Semester 7',
                'target_field' => 'it',
            ]
        );

        $latestAssessment = $user->assessments()->where('status', 'completed')->latest()->first();
        $employability = $this->skillGapService->getEmployabilityDetails($user, $latestAssessment);
        $employabilityScore = $employability['total'];

        // Skill gap analysis data
        $gapData = $this->skillGapService->analyzeSkillGap($user);
        $targetOccupation = $gapData['occupation'] ?? Occupation::first();

        // Riwayat skor asesmen per komponen
        $scoresHistory = [
            'hard_skill' => $latestAssessment ? (int) $latestAssessment->hard_skill_score : 70,
            'soft_skill' => $latestAssessment ? (int) $latestAssessment->soft_skill_score : 80,
            'total' => $latestAssessment ? (int) $latestAssessment->total_score : 75,
        ];

        // Undangan & jadwal wawancara aktif untuk mahasiswa
        $upcomingInterviews = JobApplication::where('user_id', $user->id)
            ->whereIn('status', ['Wawancara', 'Proses Seleksi'])
            ->whereNotNull('interview_date')
            ->with(['jobListing.user'])
            ->orderBy('interview_date', 'asc')
            ->get();

        return view('dashboard', [
            'user' => $user,
            'profile' => $profile,
            'employability' => $employability,
            'employabilityScore' => $employabilityScore,
            'latestAssessment' => $latestAssessment,
            'targetOccupation' => $targetOccupation,
            'gapData' => $gapData,
            'scoresHistory' => $scoresHistory,
            'portfolioCount' => $user->portfolios()->count(),
            'upcomingInterviews' => $upcomingInterviews,
        ]);
    }

    /**
     * Display Skill Gap Analysis module.
     */
    public function skillGap(Request $request): View
    {
        $user = $request->user();

        $selectedOccupationId = $request->input('occupation_id');

        if ($selectedOccupationId) {
            $targetOccupation = Occupation::find($selectedOccupationId);
        } else {
            // Ambil dari rekomendasi utama (match_percentage tertinggi) yang sudah tersimpan
            $bestMatch = SkillGapResult::where('user_id', $user->id)
                ->orderByDesc('match_percentage')
                ->first();

            $targetOccupation = $bestMatch ? $bestMatch->occupation : null;
        }

        $gapData = $this->skillGapService->analyzeSkillGap($user, $targetOccupation);
        $occupations = Occupation::orderBy('field')->get();
        $latestAssessment = $user->assessments()->where('status', 'completed')->latest()->first();

        return view('skill-gap', [
            'user' => $user,
            'gapData' => $gapData,
            'occupations' => $occupations,
            'latestAssessment' => $latestAssessment,
        ]);
    }

    /**
     * Display Career Recommendation module.
     */
    public function recommendation(Request $request): View
    {
        $user = $request->user();
        $profile = $user->studentProfile;
        $field = $profile->target_field ?? 'it';

        if ($field === 'all') {
            $occupations = Occupation::with('skills')->get();
        } else {
            $occupations = Occupation::where('field', $field)->with('skills')->get();
        }

        $autoScore = 0;
        $semester = $profile->semester ?? '';
        if (str_contains($semester, 'SMK')) {
            $autoScore = -100;
        } elseif (str_contains($semester, 'Semester') || str_contains($semester, 'Alumni')) {
            $autoScore = 100;
        }

        $recommendations = [];
        foreach ($occupations as $occ) {
            $analysis = $this->skillGapService->analyzeSkillGap($user, $occ);
            $match = $analysis['match_percentage'];

            // AI Standard Adjustment (SMK vs PT)
            $occTitle = strtolower($occ->title);
            $aiBonus = 0;
            if ($autoScore < 0) {
                // SMK: Boost technical / operational roles
                if (str_contains($occTitle, 'technician') || str_contains($occTitle, 'support') || str_contains($occTitle, 'operator') || str_contains($occTitle, 'junior') || str_contains($occTitle, 'administrator') || str_contains($occTitle, 'drafter')) {
                    $aiBonus = 20;
                } elseif (str_contains($occTitle, 'architect') || str_contains($occTitle, 'manager') || str_contains($occTitle, 'scientist') || str_contains($occTitle, 'lead') || str_contains($occTitle, 'senior')) {
                    $aiBonus = -25;
                }
            } elseif ($autoScore > 0) {
                // PT: Boost analytical / engineering roles
                if (str_contains($occTitle, 'architect') || str_contains($occTitle, 'manager') || str_contains($occTitle, 'scientist') || str_contains($occTitle, 'engineer') || str_contains($occTitle, 'analyst') || str_contains($occTitle, 'developer')) {
                    $aiBonus = 20;
                } elseif (str_contains($occTitle, 'operator') || str_contains($occTitle, 'support') || str_contains($occTitle, 'technician') || str_contains($occTitle, 'entry')) {
                    $aiBonus = -25;
                }
            }

            $finalMatch = min(98, max(10, $match + $aiBonus));

            $recommendations[] = [
                'occupation' => $occ,
                'match_percentage' => $finalMatch,
                'gap_score' => $analysis['gap_score'],
                'gap_details' => $analysis['gap_details'],
                'is_ai_recommended' => $aiBonus > 0,
            ];
        }

        // Urutkan berdasarkan matching score tertinggi
        usort($recommendations, fn ($a, $b) => $b['match_percentage'] <=> $a['match_percentage']);

        return view('recommendation', [
            'recommendations' => $recommendations,
            'profile' => $profile,
            'field' => $field,
        ]);
    }

    /**
     * Display Career Roadmap module.
     */
    public function roadmap(Request $request): View
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        if ($request->has('occupation_id')) {
            $occId = (int) $request->query('occupation_id');
            if ($profile) {
                $profile->update([
                    'target_occupation_id' => $occId,
                    'target_occupation_ids' => [$occId],
                ]);
            }
        }

        // Pastikan roadmap sudah ter-generate
        $gapData = $this->skillGapService->analyzeSkillGap($user);
        $occupation = $gapData['occupation'] ?? Occupation::first();

        $roadmaps = $user->careerRoadmaps()
            ->where('occupation_id', $occupation?->id)
            ->orderBy('stage_order')
            ->get();

        return view('roadmap', [
            'roadmaps' => $roadmaps,
            'occupation' => $occupation,
            'profile' => $profile,
            'gapData' => $gapData,
        ]);
    }

    /**
     * Toggle action item completion in roadmap.
     */
    public function toggleRoadmapItem(Request $request, CareerRoadmap $roadmap): RedirectResponse
    {
        $taskIndex = $request->input('task_index');
        $items = $roadmap->action_items ?? [];

        Log::info('toggleRoadmapItem triggered', [
            'roadmap_id' => $roadmap->id,
            'task_index' => $taskIndex,
            'isset' => isset($items[$taskIndex]),
        ]);

        if (isset($items[$taskIndex])) {
            $currentStatus = $items[$taskIndex]['status'] ?? 'pending';
            $items[$taskIndex]['status'] = ($currentStatus === 'completed') ? 'pending' : 'completed';

            // Check if all completed
            $allDone = true;
            foreach ($items as $item) {
                if (($item['status'] ?? 'pending') !== 'completed') {
                    $allDone = false;
                    break;
                }
            }

            // Force update via DB facade to bypass any Eloquent casting issues
            Log::info('saving items', ['items' => $items, 'allDone' => $allDone]);

            DB::table('career_roadmaps')
                ->where('id', $roadmap->id)
                ->update([
                    'action_items' => json_encode($items),
                    'is_completed' => $allDone,
                ]);
        }

        return redirect()->route('roadmap')->with('status', 'Progres roadmap berhasil diperbarui.');
    }

    public function jobs(Request $request): View
    {
        $user = $request->user();

        $query = JobListing::where('status', 'Buka')->with('user')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('skills', 'like', "%{$search}%");
            });
        }

        if ($industries = $request->input('industry')) {
            $query->whereIn('department', $industries);
        }

        if ($locations = $request->input('location')) {
            $query->whereIn('location', $locations);
        }

        $jobs = $query->get();

        // Ambil data assessment terakhir sebagai base score (maks 30% pembobotan)
        $latestAssessment = $user->assessments()->where('status', 'completed')->latest()->first();
        $baseScore = $latestAssessment ? (float) $latestAssessment->total_score * 0.3 : 20.0;

        // Ambil seluruh teknologi portofolio mahasiswa (dijadikan lowercase untuk pencocokan)
        $portfolios = $user->portfolios;
        $studentTechStack = [];
        foreach ($portfolios as $portfolio) {
            if (! empty($portfolio->technologies)) {
                $techs = is_string($portfolio->technologies)
                    ? json_decode($portfolio->technologies, true) ?? []
                    : $portfolio->technologies;

                foreach ($techs as $tech) {
                    $studentTechStack[] = strtolower(trim($tech));
                }
            }
        }

        $profile = $user->studentProfile;
        $studentField = strtolower($profile->target_field ?? '');

        // Ambil data lamaran pekerjaan mahasiswa (dikelompokkan berdasarkan job_listing_id)
        $userApplications = $user->jobApplications()->get()->keyBy('job_listing_id');
        $appliedJobIds = $userApplications->keys()->toArray();

        // Filter pekerjaan berdasarkan peminatan jurusan mahasiswa
        if (in_array($studentField, ['it', 'elektro'])) {
            $jobs = $jobs->filter(function ($job) use ($studentField, $appliedJobIds) {
                // Selalu tampilkan jika kandidat sudah memiliki riwayat lamaran/tawaran untuk posisi ini
                if (in_array($job->id, $appliedJobIds)) {
                    return true;
                }

                $dept = strtolower($job->department ?? '');
                $title = strtolower($job->title ?? '');

                // Kata kunci yang biasanya masuk ranah Elektro / Hardware
                $isElektro = str_contains($dept, 'hardware') || str_contains($title, 'iot')
                    || str_contains($title, 'embedded') || str_contains($title, 'plc')
                    || str_contains($title, 'electrical') || str_contains($title, 'robot')
                    || str_contains($title, 'microcontroller') || str_contains($title, 'pcb');

                if ($studentField === 'elektro') {
                    return $isElektro;
                } else {
                    // Jika IT, kembalikan yang bukan spesifik elektro
                    return ! $isElektro;
                }
            })->values();
        }

        // Tambahkan skill dari profil / CV (cv_custom_skills)
        $cvSkills = $profile->cv_custom_skills ?? [];
        foreach ($cvSkills as $cvSkill) {
            if (is_array($cvSkill) && isset($cvSkill['name'])) {
                $studentTechStack[] = strtolower(trim($cvSkill['name']));
            } elseif (is_string($cvSkill)) {
                $studentTechStack[] = strtolower(trim($cvSkill));
            }
        }
        $studentTechStack = array_unique($studentTechStack);

        // Ambil rekomendasi karier / target profesi mahasiswa
        $targetOccupationIds = $profile->target_occupation_ids ?? [];
        if (empty($targetOccupationIds) && $profile->target_occupation_id) {
            $targetOccupationIds = [$profile->target_occupation_id];
        }
        $targetOccupations = Occupation::whereIn('id', $targetOccupationIds)->pluck('title')->map(fn ($t) => strtolower($t))->toArray();

        // Kalkulasi skor kecocokan per lowongan
        $jobs->transform(function ($job) use ($baseScore, $studentTechStack, $studentField, $targetOccupations) {
            $matchedSkillsCount = 0;
            $requiredSkills = $job->skills ?? [];

            foreach ($requiredSkills as $reqSkill) {
                if (in_array(strtolower(trim($reqSkill)), $studentTechStack)) {
                    $matchedSkillsCount++;
                }
            }

            // Bonus = 15% untuk setiap skill yang cocok (Portofolio & CV)
            $skillBonus = $matchedSkillsCount * 15;

            // Bonus Bidang / Jurusan = 15-20% jika departemen sesuai dengan target field mahasiswa
            $fieldBonus = 0;
            $jobDept = strtolower($job->department ?? '');

            if ($studentField === 'it' && (str_contains($jobDept, 'it') || str_contains($jobDept, 'software') || str_contains($jobDept, 'data') || str_contains($jobDept, 'security') || str_contains($jobDept, 'mobile'))) {
                $fieldBonus = 20;
            } elseif ($studentField === 'electronics' && (str_contains($jobDept, 'hardware') || str_contains($jobDept, 'iot') || str_contains($jobDept, 'research') || str_contains($jobDept, 'engineering'))) {
                $fieldBonus = 20;
            } elseif ($studentField && (str_contains($jobDept, $studentField))) {
                $fieldBonus = 15;
            }

            // Bonus Target Karier = 25% jika lowongan ini sesuai dengan rekomendasi profesi / target
            $roleBonus = 0;
            $jobTitle = strtolower($job->title ?? '');
            foreach ($targetOccupations as $occTitle) {
                // Jika judul lowongan mengandung profesi target, atau sebaliknya
                if (str_contains($jobTitle, $occTitle) || str_contains($occTitle, $jobTitle) || similar_text($jobTitle, $occTitle) > 75) {
                    $roleBonus = 25;
                    break;
                }
            }

            // Kalkulasi akhir (Base Score + Skill Bonus + Field Bonus + Role Bonus)
            $finalScore = $baseScore + $skillBonus + $fieldBonus + $roleBonus;

            // Batasi skor maksimal 98%
            $job->match_score = min(98.0, $finalScore);

            return $job;
        });

        // Urutkan lowongan dari skor kecocokan tertinggi
        $jobs = $jobs->sortByDesc('match_score')->values();

        // Dynamic filters based on actual data
        $filterLocations = JobListing::where('status', 'Buka')
            ->select('location', DB::raw('count(*) as count'))
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(6)
            ->pluck('location');

        $filterDepartments = JobListing::where('status', 'Buka')
            ->select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('department');

        return view('jobs', [
            'jobs' => $jobs,
            'appliedJobIds' => $appliedJobIds,
            'userApplications' => $userApplications,
            'filterLocations' => $filterLocations,
            'filterDepartments' => $filterDepartments,
        ]);
    }

    /**
     * Submit an application for a job listing.
     */
    public function applyJob(Request $request, JobListing $job): RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'mahasiswa') {
            return redirect()->back()->with('error', 'Hanya akun mahasiswa yang dapat melamar pekerjaan.');
        }

        // Cek apakah sudah pernah melamar atau diundang
        $existing = JobApplication::where('user_id', $user->id)
            ->where('job_listing_id', $job->id)
            ->first();

        if ($existing) {
            // Jika statusnya 'Menunggu Review' dan 'is_invited' true, berarti belum diterima kandidat (masih pending tawaran)
            if ($existing->is_invited && $existing->status === 'Menunggu Review') {
                $existing->update([
                    'status' => 'Wawancara',
                    'notes' => 'Tawaran wawancara diterima oleh kandidat.',
                ]);

                return redirect()->back()->with('status', 'Anda telah menerima tawaran dari mitra. Status Anda sekarang: Wawancara.');
            }

            return redirect()->back()->with('info', 'Anda sudah pernah melamar posisi ini sebelumnya.');
        }

        // Hitung skor kecocokan saat melamar (disamakan dengan Employability Score / Skor Kesiapan Kerja)
        $skillGapService = app(SkillGapService::class);
        $matchScore = (float) ($user->studentProfile->employability_score ?? $skillGapService->calculateEmployabilityScore($user));

        $application = JobApplication::create([
            'user_id' => $user->id,
            'job_listing_id' => $job->id,
            'match_score' => $matchScore,
            'status' => 'Menunggu Review',
        ]);

        // Send notification to Mitra
        if ($job->user) {
            $job->user->notify(new NewJobApplicationNotification($application));
        }

        return redirect()->back()->with('status', 'Lamaran Anda untuk posisi '.$job->title.' di '.($job->user->name ?? 'Mitra').' berhasil dikirim!');
    }

    public function showJob(JobListing $job)
    {
        $job->load('user');

        $userApp = JobApplication::where('user_id', Auth::id())
            ->where('job_listing_id', $job->id)
            ->first();

        // Skor kecocokan disamakan dengan Employability Score / Skor Kesiapan Kerja mahasiswa
        $user = Auth::user();
        $skillGapService = app(SkillGapService::class);
        $job->match_score = (float) ($user->studentProfile->employability_score ?? $skillGapService->calculateEmployabilityScore($user));

        return view('job_show', compact('job', 'userApp'));
    }

    public function rejectJob(JobListing $job)
    {
        $user = Auth::user();

        $app = JobApplication::where('user_id', $user->id)
            ->where('job_listing_id', $job->id)
            ->first();

        if ($app) {
            $app->update([
                'status' => $app->is_invited ? 'Tawaran Ditolak' : 'Ditolak',
                'notes' => 'Tawaran ditolak secara otomatis oleh kandidat.',
            ]);
        } else {
            $app = JobApplication::create([
                'user_id' => $user->id,
                'job_listing_id' => $job->id,
                'match_score' => 0,
                'status' => 'Ditolak',
                'notes' => 'Tawaran ditolak secara otomatis oleh kandidat.',
            ]);
        }

        // Kirim Notifikasi ke Mitra Industri jika ada tawaran ditolak
        $job->load('user');
        if ($job->user) {
            $app->load(['user', 'jobListing']);
            $job->user->notify(new CandidateResponseNotification($app, 'Tidak Bersedia', 'Tawaran Posisi'));
        }

        return redirect()->route('jobs.show', $job)->with('status', 'Anda telah menolak tawaran posisi '.$job->title.'.');
    }

    public function applications()
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            $user->unsetRelation('unreadNotifications');
        }

        $applications = JobApplication::where('user_id', Auth::id())
            ->with('jobListing')
            ->orderByDesc('created_at')
            ->get();

        return view('applications', compact('applications'));
    }

    public function confirmInterview(Request $request, JobApplication $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'interview_status' => 'required|in:Hadir,Tidak Hadir',
        ]);

        $application->update([
            'interview_status' => $validated['interview_status'],
        ]);

        $isTest = ($application->status === 'Proses Seleksi');
        $msg = $validated['interview_status'] === 'Hadir'
            ? ($isTest ? 'Anda telah mengonfirmasi bersedia mengikuti tes seleksi.' : 'Anda telah mengonfirmasi kehadiran untuk wawancara.')
            : ($isTest ? 'Anda telah menyatakan tidak bersedia mengikuti tes seleksi.' : 'Anda telah menolak jadwal wawancara.');

        // Kirim Notifikasi ke Mitra Industri
        $application->load(['jobListing.user', 'user']);
        $mitraUser = $application->jobListing?->user;

        if ($mitraUser) {
            $responseType = ($validated['interview_status'] === 'Hadir') ? 'Bersedia' : 'Tidak Bersedia';
            $stage = $isTest ? 'Tes / Seleksi Khusus' : 'Sesi Wawancara';

            // 1. In-App Database Notification (Lonceng Notifikasi & Menu Mitra)
            $mitraUser->notify(new CandidateResponseNotification($application, $responseType, $stage));

            // 2. Email Notification ke Mitra
            try {
                $applicantName = $application->user->name ?? 'Mahasiswa';
                $jobTitle = $application->jobListing->title ?? 'Lowongan';
                $subject = "[Career Intel] Respon Kandidat: {$applicantName} - {$jobTitle}";
                $body = $responseType === 'Bersedia'
                    ? "Halo {$mitraUser->name},\n\nMahasiswa {$applicantName} telah mengonfirmasi BERSEDIA mengikuti {$stage} untuk posisi {$jobTitle}.\n\nSilakan cek detail dan kelola pelamar melalui portal mitra Anda."
                    : "Halo {$mitraUser->name},\n\nMahasiswa {$applicantName} menyatakan TIDAK BERSEDIA mengikuti {$stage} untuk posisi {$jobTitle}.\n\nSilakan cek detail pelamar melalui portal mitra Anda.";

                Mail::to($mitraUser->email)->send(new MitraNotificationMail($subject, $body));
            } catch (\Exception $e) {
                Log::warning('Gagal mengirim email respon kandidat ke mitra: '.$e->getMessage());
            }
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Download official Acceptance Letter for student.
     */
    public function downloadAcceptanceLetter(JobApplication $application): Response
    {
        if ($application->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        if ($application->status !== 'Diterima') {
            abort(400, 'Surat penerimaan hanya tersedia untuk lamaran yang berstatus Diterima.');
        }

        $application->load(['user.studentProfile', 'jobListing.user']);
        $student = $application->user;
        $profile = $student->studentProfile;
        $job = $application->jobListing;
        $mitra = $job->user;

        $companyName = $mitra->name ?? 'Perusahaan Mitra Industri';
        $companyEmail = $mitra->email ?? null;
        $companyLocation = $job->location ?? 'Indonesia';
        $issueDate = $application->updated_at ? $application->updated_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
        $refNumber = 'SPM/'.date('Y/m', strtotime($application->updated_at ?? now())).'/'.str_pad((string) $application->id, 4, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('mitra.pdf.acceptance_letter', compact(
            'application',
            'student',
            'profile',
            'job',
            'companyName',
            'companyEmail',
            'companyLocation',
            'issueDate',
            'refNumber'
        ))->setPaper('a4', 'portrait');

        $safeStudentName = Str::slug($student->name, '_');
        $safeJobTitle = Str::slug($job->title, '_');
        $fileName = "Surat_Penerimaan_{$safeStudentName}_{$safeJobTitle}.pdf";

        return $pdf->download($fileName);
    }
}
