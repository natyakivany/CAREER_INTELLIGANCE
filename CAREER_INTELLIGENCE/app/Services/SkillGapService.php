<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\CareerRoadmap;
use App\Models\Occupation;
use App\Models\SkillGapResult;
use App\Models\User;

class SkillGapService
{
    /**
     * Calculate Employability Score (0-100).
     * Formula: 40% Assessment + 35% Portfolio + 25% Profile Completeness.
     */
    public function calculateEmployabilityScore(User $user, ?Assessment $latestAssessment = null): int
    {
        $details = $this->getEmployabilityDetails($user, $latestAssessment);

        return $details['total'];
    }

    /**
     * Get detailed breakdown of Employability Score.
     */
    public function getEmployabilityDetails(User $user, ?Assessment $latestAssessment = null): array
    {
        $profile = $user->studentProfile;

        // 1. Profile score (0 - 100)
        $profileScore = 20;
        $isGraduate = $profile && in_array($profile->semester, ['Alumni / Fresh Graduate PT', 'Lulusan SMK/SMA (Siap Kerja)']);
        if ($user->nim || $isGraduate) {
            $profileScore += 20;
        }
        if ($profile && $profile->phone) {
            $profileScore += 15;
        }
        if ($profile && $profile->bio) {
            $profileScore += 20;
        }
        if ($profile && ($profile->target_occupation_id || ! empty($profile->target_occupation_ids))) {
            $profileScore += 25;
        }
        $profileScore = min(100, $profileScore);

        // 2. Portfolio score (0 - 100)
        $portfolioScore = $this->calculatePortfolioCompleteness($user);

        // 3. Assessment score (0 - 100)
        $assessment = $latestAssessment ?? $user->assessments()->where('status', 'completed')->latest()->first();
        $assessmentScore = $assessment ? (float) $assessment->total_score : 0;

        if (! $assessment) {
            // Jika belum asesmen, nilai awal berasal dari profil & portofolio (proporsional)
            $totalScore = (int) round((0.35 * $portfolioScore) + (0.25 * $profileScore));
        } else {
            $totalScore = (int) round((0.40 * $assessmentScore) + (0.35 * $portfolioScore) + (0.25 * $profileScore));
        }

        $totalScore = max(10, min(99, $totalScore));

        if ($profile && $profile->employability_score !== $totalScore) {
            $profile->update(['employability_score' => $totalScore]);
        }

        if ($assessment && $assessment->employability_score !== $totalScore) {
            $assessment->update(['employability_score' => $totalScore]);
        }

        if ($totalScore >= 80) {
            $level = 'Sangat Siap Kerja';
            $description = 'Skor kesiapan kerja Anda berada di kuartil teratas dan telah memenuhi standar industri.';
        } elseif ($totalScore >= 60) {
            $level = 'Siap Kerja';
            $description = 'Kompetensi Anda mencukupi untuk posisi pekerjaan & junior level di industri.';
        } else {
            $level = 'Perlu Persiapan';
            $description = 'Lengkapi profil, tambah portofolio, dan selesaikan asesmen untuk meningkatkan kesiapan kerja.';
        }

        return [
            'total' => $totalScore,
            'level' => $level,
            'description' => $description,
            'has_assessment' => (bool) $assessment,
            'components' => [
                'assessment' => (int) round($assessmentScore),
                'portfolio' => (int) round($portfolioScore),
                'profile' => (int) round($profileScore),
            ],
        ];
    }

    /**
     * Calculate Portfolio Completeness Score (0-100%).
     * Rule:
     * - 0 proyek = 0%
     * - 1 proyek = 35%
     * - 2 proyek = 70%
     * - 3+ proyek = 100%
     */
    public function calculatePortfolioCompleteness(User $user): int
    {
        $count = $user->portfolios()->count();

        if ($count === 0) {
            return 0;
        }

        if ($count === 1) {
            return 35;
        }

        if ($count === 2) {
            return 70;
        }

        return 100;
    }

    /**
     * Analyze skill gap for target occupation or recommend top matching roles.
     */
    public function analyzeSkillGap(User $user, ?Occupation $targetOccupation = null): array
    {
        $profile = $user->studentProfile;
        $field = $profile->target_field ?? 'it';

        // Tentukan target pekerjaan utama
        $occupation = $targetOccupation
            ?? $profile?->targetOccupation
            ?? Occupation::where('field', $field)->first()
            ?? Occupation::first();

        if (! $occupation) {
            return [];
        }

        $latestAssessment = $user->assessments()->where('status', 'completed')->latest()->first();
        $assessmentScore = $latestAssessment ? (float) $latestAssessment->total_score : 65.0;
        $portfolios = $user->portfolios()->where('field', $occupation->field)->get();

        $skills = $occupation->skills;

        // 1. Coba Generate via Gemini AI RAG + CBF
        $geminiService = app(GeminiAssessmentService::class);
        $aiResult = $geminiService->generateSkillGapAnalysis($user, $occupation, $latestAssessment, $portfolios, $skills);

        $gapDetails = [];
        $matchPercentage = 75.0;
        $gapScore = 25.0;
        $aiRecommendation = null;

        if ($aiResult && isset($aiResult['match_percentage'])) {
            // Berhasil mendapatkan hasil dari Gemini
            $matchPercentage = (float) $aiResult['match_percentage'];
            $gapScore = (float) $aiResult['gap_score'];
            $aiRecommendation = $aiResult['ai_recommendation'] ?? null;

            foreach ($aiResult['gap_details'] as $detail) {
                $detail['status'] = (int) $detail['gap'] >= 0 ? 'Memenuhi Standar' : 'Perlu Ditingkatkan';
                $gapDetails[] = $detail;
            }
        } else {
            // 2. Fallback ke Algoritma Matematis Lama jika API Gagal
            $totalUserScore = 0;
            $totalBenchmarkScore = 0;
            $assessmentScore = $latestAssessment ? (float) $latestAssessment->total_score : 65.0;

            foreach ($skills as $skill) {
                $benchmark = $skill->benchmark_score;

                // Estimasi skor kemampuan mahasiswa berbasis asesmen + portofolio pendukung
                $baseStudentScore = $assessmentScore * 0.9;
                $bonusPortfolio = min(15, $portfolios->count() * 5);
                $studentLevel = min(98, round($baseStudentScore + $bonusPortfolio + rand(-3, 5)));

                $gap = $studentLevel - $benchmark; // Negatif berarti kurang

                $gapDetails[] = [
                    'skill_name' => $skill->skill_name,
                    'benchmark' => $benchmark,
                    'student_level' => $studentLevel,
                    'gap' => $gap,
                    'status' => $gap >= 0 ? 'Memenuhi Standar' : 'Perlu Ditingkatkan',
                    'reason' => 'Dinilai berdasarkan formula heuristik portofolio dan asesmen.',
                ];

                $totalUserScore += $studentLevel;
                $totalBenchmarkScore += $benchmark;
            }

            $matchPercentage = $totalBenchmarkScore > 0
                ? round(($totalUserScore / $totalBenchmarkScore) * 100, 1)
                : 75.0;

            $matchPercentage = min(96.0, max(45.0, $matchPercentage));
            $gapScore = round(100 - $matchPercentage, 1);
        }

        // Simpan / update record ke database
        $skillGapRecord = SkillGapResult::updateOrCreate(
            [
                'user_id' => $user->id,
                'occupation_id' => $occupation->id,
            ],
            [
                'assessment_id' => $latestAssessment?->id,
                'match_percentage' => $matchPercentage,
                'gap_score' => $gapScore,
                'gap_details' => $gapDetails,
                'ai_recommendation' => $aiRecommendation,
            ]
        );

        // Buat atau perbarui Career Roadmap untuk profesi ini
        $this->generateRoadmapForOccupation($user, $occupation, $gapDetails);

        return [
            'occupation' => $occupation,
            'match_percentage' => $matchPercentage,
            'gap_score' => $gapScore,
            'gap_details' => $gapDetails,
            'ai_recommendation' => $aiRecommendation,
            'record' => $skillGapRecord,
        ];
    }

    /**
     * Generate Career Roadmap milestones for student.
     */
    public function generateRoadmapForOccupation(User $user, Occupation $occupation, array $gapDetails): void
    {
        // Temukan skill yang paling butuh ditingkatkan (gap paling minus)
        $needImprovement = array_filter($gapDetails, fn ($item) => $item['gap'] < 0);
        $topSkillToLearn = ! empty($needImprovement) ? reset($needImprovement)['skill_name'] : 'Best Practice & Arsitektur Lanjut';

        $roadmaps = [
            [
                'milestone_title' => 'Tahap 1: Penguatan Fundamental & Standar Industri',
                'stage_order' => 1,
                'description' => "Fokus menutup celah kompetensi utama pada modul {$topSkillToLearn}.",
                'action_items' => [
                    ['task' => "Pelajari materi kurikulum standar tentang {$topSkillToLearn}", 'status' => 'pending'],
                    ['task' => 'Selesaikan latihan studi kasus praktis di lab kampus', 'status' => 'pending'],
                    ['task' => 'Ikuti kuis pemahaman topik fundamental', 'status' => 'pending'],
                ],
            ],
            [
                'milestone_title' => 'Tahap 2: Implementasi Proyek Portofolio Terpandu',
                'stage_order' => 2,
                'description' => "Membangun proyek nyata berstandar industri untuk posisi {$occupation->title}.",
                'action_items' => [
                    ['task' => 'Rancang arsitektur aplikasi/sistem portofolio baru', 'status' => 'pending'],
                    ['task' => 'Unggah kode sumber ke GitHub dengan dokumentasi README rapi', 'status' => 'pending'],
                    ['task' => 'Daftarkan proyek baru ke menu Portofolio platform', 'status' => 'pending'],
                ],
            ],
            [
                'milestone_title' => 'Tahap 3: Akselerasi Rekrutmen & Penyaluran Pekerjaan',
                'stage_order' => 3,
                'description' => 'Sinkronisasi CV ATS dan pengajuan fast-track pekerjaan ke mitra industri kampus.',
                'action_items' => [
                    ['task' => 'Generate CV ATS terverifikasi dengan nilai asesmen terbaru', 'status' => 'pending'],
                    ['task' => 'Kirim lamaran pekerjaan ke lowongan mitra yang memiliki kecocokan >80%', 'status' => 'pending'],
                    ['task' => 'Simulasi wawancara teknis (Technical Interview Preparation)', 'status' => 'pending'],
                ],
            ],
        ];

        foreach ($roadmaps as $r) {
            CareerRoadmap::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'occupation_id' => $occupation->id,
                    'stage_order' => $r['stage_order'],
                ],
                $r
            );
        }
    }
}
