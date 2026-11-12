<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\StudentProfile;
use App\Services\GeminiAssessmentService;
use App\Services\SkillGapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function __construct(
        protected GeminiAssessmentService $geminiService,
        protected SkillGapService $skillGapService,
    ) {}

    /**
     * Display the assessment history.
     */
    public function history(Request $request): View
    {
        $user = $request->user();

        $assessmentsHistory = $user->assessments()
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('assessment.history', [
            'assessmentsHistory' => $assessmentsHistory,
        ]);
    }

    /**
     * Display landing for Skill Assessment (selection of IT vs Elektro).
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
        $history = $user->assessments()->latest()->take(5)->get();
        $activeAssessment = $user->assessments()->where('status', 'in_progress')->latest()->first();

        // Cek kelengkapan prasyarat: apakah profil dan portofolio sudah ada
        $hasPortfolio = $portfolios->count() > 0;
        $isGraduate = in_array($profile->semester, ['Alumni / Fresh Graduate PT', 'Lulusan SMK/SMA (Siap Kerja)']);
        $hasProfile = ! empty($user->nim) || $isGraduate;

        // Determine which assessment fields are available based on student's target_field
        $targetField = $profile->target_field ?? 'it';

        return view('assessment', [
            'profile' => $profile,
            'portfolios' => $portfolios,
            'history' => $history,
            'activeAssessment' => $activeAssessment,
            'hasPortfolio' => $hasPortfolio,
            'hasProfile' => $hasProfile,
            'targetField' => $targetField,
        ]);
    }

    /**
     * Start a new assessment session by generating questions via Gemini AI.
     */
    public function start(Request $request): RedirectResponse
    {
        $request->validate([
            'field' => ['required', 'in:it,elektro,all'],
        ]);

        $user = $request->user();
        $field = $request->field;

        // Hapus sesi assessment in_progress sebelumnya jika ada agar tidak memenuhi riwayat
        $user->assessments()->where('status', 'in_progress')->delete();

        $questionCount = 20;

        // Ambil riwayat teks pertanyaan dari 3 sesi terakhir agar tidak berulang
        $recentQuestionTexts = $user->assessments()
            ->with('questions')
            ->latest()
            ->take(3)
            ->get()
            ->pluck('questions')
            ->flatten()
            ->pluck('question_text')
            ->filter()
            ->values()
            ->toArray();

        // Buat sesi assessment baru
        $assessment = $user->assessments()->create([
            'field' => $field,
            'status' => 'in_progress',
            'total_questions' => $questionCount,
        ]);

        // Generate soal dinamis dari Gemini AI (dengan bank soal kaya dan anti-pengulangan)
        $profile = $user->studentProfile;
        $portfolios = $field === 'all'
            ? $user->portfolios()->get()
            : $user->portfolios()->where('field', $field)->get();

        $questionsData = $this->geminiService->generateQuestions(
            $field,
            $profile,
            $portfolios,
            $questionCount,
            $recentQuestionTexts
        );

        // Enforce exact 50/50 split: rebalance question types if AI didn't follow instruction precisely
        $halfCount = intdiv($questionCount, 2); // 10 for 20 questions
        $hardSkills = collect($questionsData)->where('type', 'hard_skill')->values();
        $softSkills = collect($questionsData)->where('type', 'soft_skill')->values();

        // If hard_skill has more than half, convert excess to soft_skill
        if ($hardSkills->count() > $halfCount) {
            $excess = $hardSkills->count() - $halfCount;
            for ($i = 0; $i < $excess; $i++) {
                $idx = collect($questionsData)->search(fn ($q) => ($q['type'] ?? 'hard_skill') === 'hard_skill' && ! isset($q['_rebalanced']));
                if ($idx !== false) {
                    $questionsData[$idx]['type'] = 'soft_skill';
                    $questionsData[$idx]['_rebalanced'] = true;
                }
            }
        }
        // If soft_skill has more than half, convert excess to hard_skill
        elseif ($softSkills->count() > $halfCount) {
            $excess = $softSkills->count() - $halfCount;
            for ($i = 0; $i < $excess; $i++) {
                $idx = collect($questionsData)->search(fn ($q) => ($q['type'] ?? 'hard_skill') === 'soft_skill' && ! isset($q['_rebalanced']));
                if ($idx !== false) {
                    $questionsData[$idx]['type'] = 'hard_skill';
                    $questionsData[$idx]['_rebalanced'] = true;
                }
            }
        }

        foreach ($questionsData as $q) {
            $originalOptions = $q['options'];
            $originalCorrectOption = strtoupper($q['correct_option'] ?? 'A');
            $correctText = $originalOptions[$originalCorrectOption] ?? null;

            if (is_array($originalOptions) && count($originalOptions) > 1 && $correctText) {
                $optionTexts = array_values($originalOptions);
                shuffle($optionTexts);
                $keys = array_keys($originalOptions);
                $newOptions = [];
                $newCorrectOption = 'A';

                foreach ($keys as $index => $key) {
                    $newOptions[$key] = $optionTexts[$index];
                    if ($optionTexts[$index] === $correctText) {
                        $newCorrectOption = $key;
                    }
                }
            } else {
                $newOptions = $originalOptions;
                $newCorrectOption = $originalCorrectOption;
            }

            $assessment->questions()->create([
                'question_text' => $q['question_text'],
                'type' => $q['type'] ?? 'hard_skill',
                'topic' => $q['topic'] ?? 'General',
                'options' => $newOptions,
                'correct_option' => $newCorrectOption,
                'explanation' => $q['explanation'] ?? '',
            ]);
        }

        $assessment->update([
            'total_questions' => $assessment->questions()->count(),
        ]);

        return redirect()->route('assessment.show', $assessment);
    }

    /**
     * Display assessment test questions interface.
     */
    public function show(Assessment $assessment): View|RedirectResponse
    {
        if ($assessment->user_id !== Auth::id()) {
            abort(403);
        }

        if ($assessment->status === 'completed') {
            return redirect()->route('assessment.result', $assessment);
        }

        $questions = $assessment->questions()->get();

        return view('assessment-test', [
            'assessment' => $assessment,
            'questions' => $questions,
        ]);
    }

    /**
     * Submit answers and perform automatic grading & Gemini evaluation.
     */
    public function submit(Request $request, Assessment $assessment): RedirectResponse
    {
        if ($assessment->user_id !== Auth::id()) {
            abort(403);
        }

        $answers = $request->input('answers', []);
        $questions = $assessment->questions()->get();

        $correctCount = 0;
        $hardSkillCorrect = 0;
        $hardSkillTotal = 0;
        $softSkillCorrect = 0;
        $softSkillTotal = 0;

        foreach ($questions as $q) {
            $userAns = strtoupper($answers[$q->id] ?? '');
            $isCorrect = ($userAns === $q->correct_option);

            $q->update([
                'user_answer' => $userAns ?: null,
                'is_correct' => $isCorrect,
            ]);

            if ($q->type === 'hard_skill') {
                $hardSkillTotal++;
                if ($isCorrect) {
                    $hardSkillCorrect++;
                }
            } else {
                $softSkillTotal++;
                if ($isCorrect) {
                    $softSkillCorrect++;
                }
            }

            if ($isCorrect) {
                $correctCount++;
            }
        }

        $totalQ = max(1, count($questions));

        $hardScore = $hardSkillTotal > 0 ? round(($hardSkillCorrect / $hardSkillTotal) * 100, 2) : 0;
        $softScore = $softSkillTotal > 0 ? round(($softSkillCorrect / $softSkillTotal) * 100, 2) : 0;

        // Pembobotan baru: Soft Skill 80%, Hard Skill 20%
        $totalScore = round(($softScore * 0.8) + ($hardScore * 0.2), 2);

        $assessment->update([
            'status' => 'completed',
            'correct_answers' => $correctCount,
            'hard_skill_score' => $hardScore,
            'soft_skill_score' => $softScore,
            'total_score' => $totalScore,
        ]);

        // Dapatkan umpan balik & ringkasan evaluasi cerdas dari Gemini
        $eval = $this->geminiService->generateEvaluation($assessment);
        $assessment->update([
            'ai_feedback' => $eval['feedback'],
            'ai_strengths' => $eval['strengths'],
            'ai_weaknesses' => $eval['weaknesses'],
        ]);

        // Hitung Employability Score komprehensif
        $this->skillGapService->calculateEmployabilityScore($request->user(), $assessment);

        // Komputasi Skill Gap Analysis
        $this->skillGapService->analyzeSkillGap($request->user());

        return redirect()->route('assessment.result', $assessment)->with('status', 'Asesmen berhasil diselesaikan! Skor dan evaluasi Anda telah dihitung.');
    }

    /**
     * Show assessment results and AI feedback.
     */
    public function result(Assessment $assessment): View
    {
        if ($assessment->user_id !== Auth::id()) {
            abort(403);
        }

        $questions = $assessment->questions()->get();

        return view('assessment-result', [
            'assessment' => $assessment,
            'questions' => $questions,
        ]);
    }
}
