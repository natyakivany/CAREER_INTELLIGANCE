<?php

namespace App\Http\Controllers;

use App\Mail\JobApplicationStatusMail;
use App\Models\ActivityLog;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Occupation;
use App\Models\PendingProfileUpdate;
use App\Models\Portfolio;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\JobApprovalRequested;
use App\Notifications\ProfileUpdateRequested;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MitraController extends Controller
{
    /**
     * Mitra Dashboard — real overview from database.
     */
    public function dashboard(): View
    {
        $user = Auth::user();
        $jobs = $user->jobListings()->latest()->get();
        $jobCount = $jobs->count();
        $openJobs = $jobs->where('status', 'Buka')->values();
        $openJobCount = $openJobs->count();

        // Get all students with profiles for candidate stats
        $students = User::where('role', 'mahasiswa')
            ->with(['studentProfile.targetOccupation', 'portfolios', 'assessments' => fn ($q) => $q->where('status', 'completed')->latest()])
            ->get();

        $studentsWithPortfolio = $students->filter(fn ($s) => $s->portfolios->isNotEmpty())->count();

        // Get candidates evaluated against the company's open vacancies
        $allCandidates = $students
            ->filter(fn ($s) => $s->studentProfile)
            ->map(function ($student) use ($openJobs) {
                $latestAssessment = $student->assessments->first();
                $profile = $student->studentProfile;

                // Match against company's open jobs
                $bestJobMatch = $openJobs->isNotEmpty()
                    ? $this->findBestJobMatch($student, $openJobs)
                    : null;

                $empScore = (int) ($latestAssessment->employability_score ?? $profile->employability_score ?? 0);
                $matchScore = $bestJobMatch ? $bestJobMatch['match_score'] : $empScore;

                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nim' => $student->nim ?? '-',
                    'prodi' => ($profile->prodi ?? 'N/A').($profile->semester ? ' • '.$profile->semester : ''),
                    'role' => $profile->targetOccupation?->title ?? 'Belum ditentukan',
                    'matchScore' => $matchScore,
                    'employabilityScore' => $empScore,
                    'assessmentScore' => round($latestAssessment->total_score ?? 0),
                    'assessmentDate' => $latestAssessment ? $latestAssessment->created_at?->translatedFormat('d M Y') : null,
                    'assessmentDateHuman' => $latestAssessment ? $latestAssessment->created_at?->diffForHumans() : null,
                    'status' => $empScore >= 80 ? 'Siap Kerja' : ($empScore >= 50 ? 'Berkembang' : 'Baru Mulai'),
                    'portfolio' => $student->portfolios->first()?->title ?? 'Belum ada portofolio',
                    'github' => $student->portfolios->first()?->repository_url ?? '#',
                    'avatarUrl' => $student->avatar_url,
                    'topSkills' => $this->extractTopSkills($student),
                    'recommendedJob' => $bestJobMatch,
                    'notes' => $latestAssessment?->ai_feedback ? mb_substr($latestAssessment->ai_feedback, 0, 120).'...' : 'Belum ada catatan.',
                ];
            });

        $avgScore = $allCandidates->isNotEmpty()
            ? round($allCandidates->avg('matchScore'), 1)
            : 0;

        $recentCandidates = $allCandidates
            ->sortByDesc('matchScore')
            ->take(6)
            ->values();

        $recentActivityLogs = Auth::user()->activityLogs()->latest()->take(5)->get();

        return view('mitra.dashboard', compact(
            'jobs',
            'openJobs',
            'jobCount',
            'openJobCount',
            'students',
            'studentsWithPortfolio',
            'recentCandidates',
            'avgScore',
            'recentActivityLogs'
        ));
    }

    /**
     * Kelola Lowongan — list all job listings with create form and applicants.
     */
    public function jobs(): View
    {
        $jobs = Auth::user()->jobListings()
            ->latest()
            ->get();

        return view('mitra.jobs', compact('jobs'));
    }

    /**
     * Kelola Pelamar — list all applications for mitra's jobs.
     */
    public function applications(Request $request): View
    {
        $sort = $request->query('sort', 'terbaru');

        $hasUnreadApplicants = Auth::check() && Auth::user()->unreadNotifications()
            ->where(function ($q) {
                $q->where('data->type', 'candidate_application')
                    ->orWhere('type', 'App\Notifications\NewJobApplicationNotification')
                    ->orWhereNotNull('data->applicant_name');
            })
            ->exists();

        if (Auth::check() && Auth::user()) {
            Auth::user()->unreadNotifications->markAsRead();
            Auth::user()->unsetRelation('unreadNotifications');
        }

        $rawJobs = Auth::user()->jobListings()
            ->with(['applications.user.studentProfile.targetOccupation', 'applications.user.portfolios', 'applications.user.assessments' => fn ($q) => $q->where('status', 'completed')->latest()])
            ->get();

        // Urutkan lowongan:
        // Prioritas Utama: Posisi yang ada pelamar baru ('Menunggu Review') SELALU di barisan paling awal
        // Opsi Urutan: Berdasarkan Pelamar Terbaru vs Pelamar Terlama
        $jobs = $rawJobs->sort(function ($a, $b) use ($sort) {
            $aNew = $a->applications->where('status', 'Menunggu Review')->count();
            $bNew = $b->applications->where('status', 'Menunggu Review')->count();

            $aHasNew = $aNew > 0 ? 1 : 0;
            $bHasNew = $bNew > 0 ? 1 : 0;

            // Prioritas 1: Posisi yang memiliki pelamar baru status 'Menunggu Review' SELALU di atas
            if ($aHasNew !== $bHasNew) {
                return $bHasNew <=> $aHasNew; // 1 (ada pelamar baru) lebih dulu daripada 0
            }

            // Prioritas 2: Urutan Pelamar Terbaru vs Terlama
            if ($sort === 'terlama') {
                // Diurutkan berdasarkan pelamar yang melamar paling awal (terlama)
                $aTime = $aHasNew
                    ? ($a->applications->where('status', 'Menunggu Review')->sortBy('created_at')->first()?->created_at?->timestamp ?? PHP_INT_MAX)
                    : ($a->applications->sortBy('created_at')->first()?->created_at?->timestamp ?? PHP_INT_MAX);

                $bTime = $bHasNew
                    ? ($b->applications->where('status', 'Menunggu Review')->sortBy('created_at')->first()?->created_at?->timestamp ?? PHP_INT_MAX)
                    : ($b->applications->sortBy('created_at')->first()?->created_at?->timestamp ?? PHP_INT_MAX);

                if ($aTime !== $bTime) {
                    return $aTime <=> $bTime; // Timestamp terkecil (paling awal melamar) didahulukan
                }
            } else {
                // Default 'terbaru': Diurutkan berdasarkan pelamar yang melamar paling baru
                $aTime = $aHasNew
                    ? ($a->applications->where('status', 'Menunggu Review')->sortByDesc('created_at')->first()?->created_at?->timestamp ?? 0)
                    : ($a->applications->sortByDesc('created_at')->first()?->created_at?->timestamp ?? 0);

                $bTime = $bHasNew
                    ? ($b->applications->where('status', 'Menunggu Review')->sortByDesc('created_at')->first()?->created_at?->timestamp ?? 0)
                    : ($b->applications->sortByDesc('created_at')->first()?->created_at?->timestamp ?? 0);

                if ($aTime !== $bTime) {
                    return $bTime <=> $aTime; // Timestamp terbesar (paling baru melamar) didahulukan
                }
            }

            // Prioritas 3: Jumlah pelamar baru terbanyak
            if ($aNew !== $bNew) {
                return $bNew <=> $aNew;
            }

            // Prioritas 4: Jumlah pelamar yang sedang dalam proses
            $aInProcess = $a->applications->whereIn('status', ['Menunggu Review', 'Proses Seleksi', 'Wawancara'])->count();
            $bInProcess = $b->applications->whereIn('status', ['Menunggu Review', 'Proses Seleksi', 'Wawancara'])->count();

            if ($aInProcess !== $bInProcess) {
                return $bInProcess <=> $aInProcess;
            }

            // Prioritas 5: Tanggal buat lowongan
            return $b->created_at <=> $a->created_at;
        })->values();

        return view('mitra.applications', compact('jobs', 'hasUnreadApplicants', 'sort'));
    }

    /**
     * Show applicants for a specific job.
     */
    public function showJobApplications(Request $request, JobListing|string $job): View|RedirectResponse
    {
        if (! ($job instanceof JobListing)) {
            $foundJob = JobListing::find($job);
            if (! $foundJob) {
                // Check if an application ID was passed instead of a job ID
                $app = JobApplication::find($job);
                if ($app && $app->jobListing && $app->jobListing->user_id === Auth::id()) {
                    return redirect()->route('mitra.applications.show', $app->jobListing);
                }

                return redirect()->route('mitra.applications.index')->with('error', 'Lowongan pekerjaan tidak ditemukan.');
            }
            $job = $foundJob;
        }

        // Ensure the job belongs to this mitra
        if ($job->user_id !== Auth::id()) {
            return redirect()->route('mitra.applications.index')->with('error', 'Anda tidak memiliki izin untuk mengelola lowongan ini.');
        }

        if (Auth::check() && Auth::user()) {
            Auth::user()->unreadNotifications->markAsRead();
            Auth::user()->unsetRelation('unreadNotifications');
        }

        $sort = $request->query('sort', 'terbaru');
        $orderDirection = $sort === 'terlama' ? 'asc' : 'desc';

        $job->load([
            'applications' => fn ($q) => $q->orderByRaw("CASE WHEN status = 'Menunggu Review' THEN 0 WHEN status IN ('Proses Seleksi', 'Wawancara') THEN 1 ELSE 2 END")
                ->orderBy('created_at', $orderDirection),
            'applications.user.studentProfile.targetOccupation',
            'applications.user.portfolios',
            'applications.user.assessments' => fn ($q) => $q->where('status', 'completed')->latest(),
        ]);

        return view('mitra.applications_show', compact('job', 'sort'));
    }

    /**
     * Fitur Tersendiri: Kelola Semua Kandidat Diterima across all jobs.
     */
    public function acceptedCandidates(Request $request): View
    {
        session(['mitra_viewed_accepted_at' => now()]);
        $mitraId = Auth::id();

        $query = JobApplication::whereHas('jobListing', function ($q) use ($mitraId) {
            $q->where('user_id', $mitraId);
        })->where('status', 'Diterima')
            ->with([
                'user.studentProfile.targetOccupation',
                'user.portfolios',
                'user.assessments' => fn ($q) => $q->where('status', 'completed')->latest(),
                'jobListing',
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhereHas('studentProfile', function ($sp) use ($search) {
                        $sp->where('prodi', 'like', "%{$search}%")
                            ->orWhere('jurusan', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('job_id')) {
            $query->where('job_listing_id', $request->job_id);
        }

        $sort = $request->get('sort', 'latest');
        if ($sort === 'score_desc') {
            $query->orderByDesc('match_score');
        } elseif ($sort === 'score_asc') {
            $query->orderBy('match_score');
        } elseif ($sort === 'name_asc') {
            $query->join('users', 'job_applications.user_id', '=', 'users.id')
                ->orderBy('users.name', 'asc')
                ->select('job_applications.*');
        } else {
            $query->latest('job_applications.updated_at');
        }

        $applications = $query->paginate(10)->withQueryString();

        $jobs = Auth::user()->jobListings()->latest()->get();

        $totalAccepted = JobApplication::whereHas('jobListing', fn ($q) => $q->where('user_id', $mitraId))->where('status', 'Diterima')->count();
        $avgScore = round(JobApplication::whereHas('jobListing', fn ($q) => $q->where('user_id', $mitraId))->where('status', 'Diterima')->avg('match_score') ?? 0);
        $distinctJobsCount = JobApplication::whereHas('jobListing', fn ($q) => $q->where('user_id', $mitraId))->where('status', 'Diterima')->distinct('job_listing_id')->count('job_listing_id');

        return view('mitra.accepted_candidates', compact(
            'applications',
            'jobs',
            'totalAccepted',
            'avgScore',
            'distinctJobsCount'
        ));
    }

    /**
     * Fitur Tersendiri: Arsip & Kelola Kandidat Ditolak across all jobs.
     */
    public function rejectedCandidates(Request $request): View
    {
        session(['mitra_viewed_rejected_at' => now()]);
        $mitraId = Auth::id();

        $query = JobApplication::whereHas('jobListing', function ($q) use ($mitraId) {
            $q->where('user_id', $mitraId);
        })->whereIn('status', ['Ditolak', 'Tawaran Ditolak'])
            ->with([
                'user.studentProfile.targetOccupation',
                'user.portfolios',
                'user.assessments' => fn ($q) => $q->where('status', 'completed')->latest(),
                'jobListing',
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhereHas('studentProfile', function ($sp) use ($search) {
                        $sp->where('prodi', 'like', "%{$search}%")
                            ->orWhere('jurusan', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('job_id')) {
            $query->where('job_listing_id', $request->job_id);
        }

        $sort = $request->get('sort', 'latest');
        if ($sort === 'score_desc') {
            $query->orderByDesc('match_score');
        } elseif ($sort === 'score_asc') {
            $query->orderBy('match_score');
        } else {
            $query->latest('job_applications.updated_at');
        }

        $applications = $query->paginate(10)->withQueryString();

        $jobs = Auth::user()->jobListings()->latest()->get();

        $totalRejected = JobApplication::whereHas('jobListing', fn ($q) => $q->where('user_id', $mitraId))->whereIn('status', ['Ditolak', 'Tawaran Ditolak'])->count();
        $avgScore = round(JobApplication::whereHas('jobListing', fn ($q) => $q->where('user_id', $mitraId))->whereIn('status', ['Ditolak', 'Tawaran Ditolak'])->avg('match_score') ?? 0);

        return view('mitra.rejected_candidates', compact(
            'applications',
            'jobs',
            'totalRejected',
            'avgScore'
        ));
    }

    /**
     * Download official Acceptance Letter (Surat Keterangan Diterima) as valid PDF.
     */
    public function downloadAcceptanceLetter(JobApplication $application): Response
    {
        if ($application->jobListing->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $application->load(['user.studentProfile', 'jobListing']);
        $student = $application->user;
        $profile = $student->studentProfile;
        $job = $application->jobListing;
        $mitra = Auth::user();

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

    /**
     * Cari Kandidat Mahasiswa — search students by skill, prodi, score, and open job compatibility.
     */
    public function candidates(Request $request): View
    {
        $openJobs = Auth::user()->jobListings()->where('status', 'Buka')->latest()->get();

        $selectedJob = null;
        if ($request->filled('job_id')) {
            $selectedJob = $openJobs->firstWhere('id', (int) $request->job_id);
        }

        $query = User::where('role', 'mahasiswa')
            ->with(['studentProfile.targetOccupation', 'portfolios', 'assessments' => fn ($q) => $q->where('status', 'completed')->latest()]);

        // Filter by program studi
        if ($request->filled('prodi')) {
            $query->whereHas('studentProfile', fn ($q) => $q->where('prodi', 'like', '%'.$request->prodi.'%'));
        }

        // Filter by skill (search in portfolios tech_stack, custom skills, or bio)
        if ($request->filled('skill')) {
            $skill = $request->skill;
            $query->where(function ($q) use ($skill) {
                $q->whereHas('portfolios', fn ($pq) => $pq->where('tech_stack', 'like', '%'.$skill.'%'))
                    ->orWhereHas('studentProfile', fn ($pq) => $pq->where('bio', 'like', '%'.$skill.'%')->orWhere('cv_custom_skills', 'like', '%'.$skill.'%'));
            });
        }

        // Filter by status kesiapan (employability score)
        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('assessments', function ($q) use ($status) {
                $q->where('status', 'completed');
                if ($status === 'Siap Kerja') {
                    $q->where('employability_score', '>=', 80);
                } elseif ($status === 'Berkembang') {
                    $q->where('employability_score', '>=', 50)->where('employability_score', '<', 80);
                } elseif ($status === 'Baru Mulai') {
                    $q->where('employability_score', '<', 50);
                }
            });
        }

        $students = $query->get()->map(function ($student) use ($openJobs, $selectedJob) {
            $latestAssessment = $student->assessments->first();
            $profile = $student->studentProfile;

            // Calculate job match: specific selected job OR best match among all open jobs
            $jobMatch = null;
            if ($selectedJob) {
                $jobMatch = $this->calculateCandidateJobMatch($student, $selectedJob);
            } elseif ($openJobs->isNotEmpty()) {
                $jobMatch = $this->findBestJobMatch($student, $openJobs);
            }

            $empScore = (int) ($latestAssessment->employability_score ?? $profile->employability_score ?? 0);
            $matchScore = $jobMatch ? $jobMatch['match_score'] : $empScore;

            return [
                'id' => $student->id,
                'name' => $student->name,
                'nim' => $student->nim ?? '-',
                'prodi' => $profile->prodi ?? 'N/A',
                'semester' => $profile->semester ?? '',
                'jurusan' => $profile->jurusan ?? '',
                'role' => $profile->targetOccupation?->title ?? 'Belum ditentukan',
                'matchScore' => $matchScore,
                'employabilityScore' => $empScore,
                'assessmentScore' => round($latestAssessment->total_score ?? 0),
                'assessmentDate' => $latestAssessment ? $latestAssessment->created_at?->translatedFormat('d M Y') : null,
                'assessmentDateFull' => $latestAssessment ? $latestAssessment->created_at?->translatedFormat('d F Y, H:i').' WIB' : null,
                'assessmentDateHuman' => $latestAssessment ? $latestAssessment->created_at?->diffForHumans() : null,
                'portfolioCount' => $student->portfolios->count(),
                'portfolio' => $student->portfolios->first()?->title ?? 'Belum ada portofolio',
                'github' => $student->portfolios->first()?->repository_url ?? '#',
                'avatarUrl' => $student->avatar_url,
                'topSkills' => $this->extractTopSkills($student),
                'recommendedJob' => $jobMatch,
                'notes' => $latestAssessment?->ai_feedback ? mb_substr($latestAssessment->ai_feedback, 0, 120).'...' : 'Belum ada catatan.',
                'status' => $empScore >= 80 ? 'Siap Kerja' : ($empScore >= 50 ? 'Berkembang' : 'Baru Mulai'),
            ];
        })->sortByDesc('matchScore')->values();

        // Define specific prodi groups as requested by user
        $prodiList = [
            'Jurusan TI' => [
                'Teknik Rekayasa Perangkat Lunak',
                'Teknik Komputer',
                'Manajemen Informatika',
                'Animasi',
            ],
            'Jurusan Elektro' => [
                'Teknik Elektronika',
                'Teknik Telekomunikasi',
                'Teknik Listrik',
            ],
        ];

        return view('mitra.candidates', compact('students', 'prodiList', 'openJobs', 'selectedJob'));
    }

    /**
     * Show full profile of a candidate
     */
    public function showCandidate(User $user): View
    {
        if ($user->role !== 'mahasiswa') {
            abort(404, 'Kandidat tidak ditemukan.');
        }

        $user->load(['studentProfile.targetOccupation', 'portfolios', 'assessments' => fn ($q) => $q->where('status', 'completed')->latest()]);

        $profile = $user->studentProfile;
        $latestAssessment = $user->assessments->first();
        $topSkills = $this->extractTopSkills($user);
        $mitraJobs = Auth::user()->jobListings()->where('status', 'Buka')->latest()->get();

        // Calculate job compatibility for each open vacancy
        $jobCompatibilities = $mitraJobs->map(function ($job) use ($user) {
            return $this->calculateCandidateJobMatch($user, $job);
        })->sortByDesc('match_score')->values();

        return view('mitra.candidates_show', [
            'student' => $user,
            'profile' => $profile,
            'latestAssessment' => $latestAssessment,
            'topSkills' => $topSkills,
            'mitraJobs' => $mitraJobs,
            'jobCompatibilities' => $jobCompatibilities,
        ]);
    }

    /**
     * Show CV of a candidate
     */
    public function candidateCv(User $user): View|RedirectResponse
    {
        if ($user->role !== 'mahasiswa') {
            abort(404, 'Kandidat tidak ditemukan.');
        }

        $user->load(['studentProfile.targetOccupation', 'portfolios', 'assessments' => fn ($q) => $q->where('status', 'completed')->latest()]);

        $profile = $user->studentProfile;

        if (! $profile?->isCvFilled()) {
            return redirect()->route('mitra.candidates.show', $user->id)
                ->with('error', 'Dokumen CV ATS kandidat ini belum aktif karena data CV belum diisi oleh kandidat.');
        }

        $portfolios = $user->portfolios;
        $latestAssessment = $user->assessments->first();
        $targetOccupation = $profile?->targetOccupation ?? Occupation::first();

        return view('cv-print', [
            'user' => $user,
            'profile' => $profile,
            'portfolios' => $portfolios,
            'latestAssessment' => $latestAssessment,
            'targetOccupation' => $targetOccupation,
        ]);
    }

    /**
     * Mengirim email undangan/tawaran dari Mitra ke Mahasiswa
     */
    public function contactCandidate(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'job_id' => 'nullable|exists:job_listings,id',
        ]);

        $mitraName = Auth::user()->name;
        $emailBody = $request->message;

        // Jika Mitra memilih lowongan, sertakan link pendaftaran dan buat record application (is_invited = true)
        if ($request->filled('job_id')) {
            $job = JobListing::find($request->job_id);
            if ($job) {
                // Buat record lamaran sebagai "diundang" tapi status masih "Menunggu Review"
                JobApplication::firstOrCreate(
                    ['user_id' => $user->id, 'job_listing_id' => $job->id],
                    [
                        'match_score' => 0,
                        'status' => 'Menunggu Review',
                        'is_invited' => true,
                        'notes' => 'Diundang oleh Mitra Industri.',
                    ]
                );

                $jobUrl = url('/jobs/'.$job->id);
                $emailBody .= "\n\n---\n";
                $emailBody .= "📋 Lowongan: {$job->title}\n";
                $emailBody .= "📍 Lokasi: {$job->location}\n";
                $emailBody .= "⏱ Durasi: {$job->duration}\n";
                $emailBody .= "\n🔗 Lihat & Terima Tawaran di: {$jobUrl}\n";
            }
        }

        Mail::raw(
            $emailBody,
            function ($mail) use ($user, $mitraName, $request) {
                $mail->to($user->email)
                    ->subject($request->subject.' - dari '.$mitraName);
            }
        );

        ActivityLog::record(
            action: 'candidate_contacted',
            title: 'Menghubungi Kandidat',
            description: "Mengirim email tawaran ke kandidat mahasiswa {$user->name}",
            subject: $user,
            properties: ['candidate_name' => $user->name, 'subject' => $request->subject]
        );

        return back()->with('success', "Email tawaran berhasil dikirim ke {$user->name}!");
    }

    /**
     * Profil Perusahaan — edit company profile.
     */
    public function profile(): View
    {
        return view('mitra.profile');
    }

    /**
     * Update Profil Perusahaan.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.Auth::id(),
        ]);

        // Instead of updating the user directly, create a pending request
        $pendingUpdate = PendingProfileUpdate::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => 'pending',
        ]);

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new ProfileUpdateRequested($pendingUpdate));
        }

        ActivityLog::record(
            action: 'profile_update_requested',
            title: 'Pengajuan Perubahan Profil',
            description: "Mengajukan permohonan perubahan profil mitra ke admin (Nama baru: {$validated['name']})",
            subject: $pendingUpdate
        );

        return redirect()->route('mitra.profile')->with('success', 'Pengajuan perubahan profil berhasil dikirim! Menunggu persetujuan Admin.');
    }

    /**
     * Store a newly created job listing.
     */
    public function storeJob(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'skills' => 'nullable|string',
        ]);

        $skills = [];
        if (! empty($validated['skills'])) {
            $skills = array_map('trim', explode(',', $validated['skills']));
        }

        $job = Auth::user()->jobListings()->create([
            ...$validated,
            'skills' => $skills,
            'status' => 'Menunggu Persetujuan',
        ]);

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new JobApprovalRequested($job));
        }

        ActivityLog::record(
            action: 'job_created',
            title: 'Membuat Lowongan Baru',
            description: "Membuat lowongan baru '{$job->title}' ({$job->type}) di departemen {$job->department}",
            subject: $job,
            properties: ['title' => $job->title, 'type' => $job->type, 'location' => $job->location]
        );

        return redirect()->route('mitra.jobs')->with('success', 'Lowongan berhasil dibuat dan sedang Menunggu Persetujuan Admin!');
    }

    /**
     * Update an existing job listing.
     */
    public function updateJob(Request $request, JobListing $job): RedirectResponse
    {
        if ($job->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'skills' => 'nullable|string',
            'status' => 'required|in:Buka,Tutup,Menunggu Persetujuan',
        ]);

        // Prevent Mitra from bypassing approval
        if ($job->status === 'Menunggu Persetujuan' && $validated['status'] === 'Buka') {
            $validated['status'] = 'Menunggu Persetujuan';
        }

        $skills = [];
        if (! empty($validated['skills'])) {
            $skills = array_map('trim', explode(',', $validated['skills']));
        }

        $job->update([
            ...$validated,
            'skills' => $skills,
        ]);

        ActivityLog::record(
            action: 'job_updated',
            title: 'Memperbarui Lowongan',
            description: "Memperbarui lowongan '{$job->title}' (Status: {$job->status})",
            subject: $job,
            properties: ['title' => $job->title, 'status' => $job->status]
        );

        return redirect()->route('mitra.jobs')->with('success', 'Lowongan berhasil diperbarui!');
    }

    /**
     * Delete a job listing.
     */
    public function destroyJob(JobListing $job): RedirectResponse
    {
        if ($job->user_id !== Auth::id()) {
            abort(403);
        }

        $jobTitle = $job->title;
        $job->delete();

        ActivityLog::record(
            action: 'job_deleted',
            title: 'Menghapus Lowongan',
            description: "Menghapus lowongan '{$jobTitle}'",
            properties: ['deleted_job_title' => $jobTitle]
        );

        return redirect()->route('mitra.jobs')->with('success', 'Lowongan berhasil dihapus!');
    }

    /**
     * Update application status (Menunggu Review, Diterima, Ditolak).
     */
    public function updateApplicationStatus(Request $request, JobApplication $application): RedirectResponse
    {
        if ($application->jobListing->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $validated = $request->validate([
            'status' => 'required|in:Menunggu Review,Proses Seleksi,Wawancara,Diterima,Ditolak',
            'interview_date' => 'nullable|date',
            'interview_type' => 'nullable|in:online,offline',
            'interview_link' => 'nullable|url',
            'interview_location' => 'nullable|string|max:500',
        ]);

        $updateData = [
            'status' => $validated['status'],
        ];

        if (in_array($validated['status'], ['Wawancara', 'Proses Seleksi'])) {
            if ($application->status !== $validated['status'] || $request->filled('interview_date') || empty($application->interview_status)) {
                $updateData['interview_status'] = 'Menunggu Konfirmasi';
            }
            if ($request->filled('interview_date')) {
                $updateData['interview_date'] = $validated['interview_date'];
            }
            $selectedType = $request->input('interview_type');

            if ($selectedType === 'offline' || ! $request->filled('interview_link')) {
                $updateData['interview_type'] = 'offline';
                $updateData['interview_link'] = null;
            } else {
                $updateData['interview_type'] = 'online';
                $updateData['interview_link'] = $validated['interview_link'];
            }
            if ($request->has('interview_location')) {
                $updateData['interview_location'] = $validated['interview_location'];
            }
        }

        $application->update($updateData);
        $application->refresh();

        // 1. In-app Notification (Selalu dikirimkan ke lonceng notifikasi database mahasiswa)
        try {
            $application->user->notify(new ApplicationStatusUpdated($application));
        } catch (\Throwable $e) {
            Log::error('Failed to create in-app notification: '.$e->getMessage());
        }

        // 2. Email Notification (Dipisah agar kegagalan SMTP/jaringan tidak membatalkan notifikasi sistem)
        try {
            Mail::to($application->user->email)->send(new JobApplicationStatusMail($application));
        } catch (\Throwable $e) {
            Log::warning('Failed to send status update email: '.$e->getMessage());
        }

        $statusLabel = match ($validated['status']) {
            'Proses Seleksi' => 'Proses Tes / Seleksi',
            'Wawancara' => 'Tahap Wawancara',
            'Diterima' => 'Diterima Magang / Kerja',
            'Ditolak' => 'Ditolak',
            default => 'Menunggu Review',
        };

        $actionKey = match ($validated['status']) {
            'Wawancara' => 'interview_scheduled',
            'Diterima' => 'application_accepted',
            'Ditolak' => 'application_rejected',
            'Proses Seleksi' => 'application_selection',
            default => 'application_status_updated',
        };

        $logTitle = match ($validated['status']) {
            'Wawancara' => 'Penjadwalan Wawancara',
            'Diterima' => 'Kandidat Diterima',
            'Ditolak' => 'Lamaran Ditolak',
            'Proses Seleksi' => 'Lolos ke Tahap Tes/Seleksi',
            default => 'Pembaruan Status Lamaran',
        };

        $logDesc = match ($validated['status']) {
            'Wawancara' => "Menjadwalkan wawancara {$application->interview_type_label} untuk pelamar {$application->user->name} pada lowongan '{$application->jobListing->title}'",
            'Diterima' => "Menerima pelamar {$application->user->name} untuk magang/kerja pada lowongan '{$application->jobListing->title}'",
            'Ditolak' => "Menolak lamaran {$application->user->name} pada lowongan '{$application->jobListing->title}'",
            'Proses Seleksi' => "Meloloskan berkas {$application->user->name} ke tahap tes/seleksi khusus pada lowongan '{$application->jobListing->title}'",
            default => "Memperbarui status lamaran {$application->user->name} menjadi '{$validated['status']}' pada lowongan '{$application->jobListing->title}'",
        };

        ActivityLog::record(
            action: $actionKey,
            title: $logTitle,
            description: $logDesc,
            subject: $application,
            properties: [
                'status' => $validated['status'],
                'applicant_name' => $application->user->name,
                'job_title' => $application->jobListing->title,
                'interview_type' => $application->interview_type ?? null,
                'interview_date' => $application->interview_date?->format('Y-m-d H:i') ?? null,
            ]
        );

        $flashMessage = match ($validated['status']) {
            'Diterima' => "Kandidat {$application->user->name} berhasil Diterima dan telah dipindahkan ke menu 'Kandidat Diterima'!",
            'Ditolak' => "Lamaran {$application->user->name} Ditolak dan telah dipindahkan ke menu 'Kandidat Ditolak'!",
            'Wawancara' => $application->interview_date
                ? "Jadwal wawancara untuk {$application->user->name} berhasil disimpan pada ".Carbon::parse($application->interview_date)->translatedFormat('l, d F Y - H:i').' WIB dan notifikasi telah dikirimkan ke pelamar!'
                : "Status lamaran {$application->user->name} berhasil diperbarui menjadi: Tahap Wawancara!",
            default => "Status lamaran {$application->user->name} berhasil diperbarui menjadi: {$statusLabel}!",
        };

        return redirect()->back()->with('success', $flashMessage);
    }

    /**
     * Delete an application.
     */
    public function destroyApplication(JobApplication $application): RedirectResponse
    {
        if ($application->jobListing->user_id !== Auth::id()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $applicantName = $application->user->name ?? 'Mahasiswa';
        $jobTitle = $application->jobListing->title ?? 'Lowongan';

        $application->delete();

        ActivityLog::record(
            action: 'application_deleted',
            title: 'Menghapus Berkas Pelamar',
            description: "Menghapus berkas lamaran {$applicantName} dari posisi '{$jobTitle}'"
        );

        return redirect()->back()->with('success', 'Data pelamar berhasil dihapus.');
    }

    /**
     * Extract top skills from a student's portfolio tech stacks.
     *
     * @return array<int, string>
     */
    private function extractTopSkills(User $student): array
    {
        $skills = [];
        foreach ($student->portfolios as $portfolio) {
            if ($portfolio->tech_stack) {
                $items = array_map('trim', explode(',', $portfolio->tech_stack));
                $skills = array_merge($skills, $items);
            }
        }

        return array_values(array_unique(array_slice($skills, 0, 5)));
    }

    /**
     * Get all unique skills known for a student (portfolios, CV custom skills).
     *
     * @return array<int, string>
     */
    private function getAllStudentSkills(User $student): array
    {
        $skills = [];
        foreach ($student->portfolios as $portfolio) {
            if ($portfolio->tech_stack) {
                $items = array_map('trim', explode(',', $portfolio->tech_stack));
                $skills = array_merge($skills, $items);
            }
        }

        $profile = $student->studentProfile;
        if ($profile && is_array($profile->cv_custom_skills)) {
            $skills = array_merge($skills, $profile->cv_custom_skills);
        }

        return array_values(array_unique(array_filter(array_map('trim', $skills))));
    }

    /**
     * Calculate match compatibility between a student and a specific open job listing.
     *
     * @return array{
     *     job_id: int,
     *     job_title: string,
     *     job_department: ?string,
     *     match_score: int,
     *     matched_skills: array<int, string>,
     *     missing_skills: array<int, string>,
     *     matched_skills_count: int,
     *     total_required_skills: int
     * }
     */
    public function calculateCandidateJobMatch(User $student, JobListing $job): array
    {
        $studentSkills = $this->getAllStudentSkills($student);
        $studentSkillsLower = array_map('strtolower', $studentSkills);

        $jobSkills = is_array($job->skills) ? $job->skills : [];
        $matchedSkills = [];
        $missingSkills = [];

        foreach ($jobSkills as $reqSkill) {
            $reqLower = strtolower(trim($reqSkill));
            $matched = false;
            foreach ($studentSkillsLower as $sSkill) {
                if ($sSkill === $reqLower || str_contains($sSkill, $reqLower) || str_contains($reqLower, $sSkill)) {
                    $matched = true;
                    break;
                }
            }
            if ($matched) {
                $matchedSkills[] = $reqSkill;
            } else {
                $missingSkills[] = $reqSkill;
            }
        }

        $totalReqSkills = count($jobSkills);
        $skillMatchRatio = $totalReqSkills > 0 ? (count($matchedSkills) / $totalReqSkills) : 0.5;
        $skillScore = $skillMatchRatio * 45; // Max 45 points

        // Target Occupation & Role alignment (Max 25 points)
        $roleScore = 0;
        $jobTitle = strtolower($job->title ?? '');
        $targetTitles = [];
        $profile = $student->studentProfile;

        if ($profile) {
            if ($profile->targetOccupation) {
                $targetTitles[] = strtolower($profile->targetOccupation->title);
            }
            if (! empty($profile->target_occupation_ids)) {
                $moreOccs = Occupation::whereIn('id', $profile->target_occupation_ids)->pluck('title')->toArray();
                foreach ($moreOccs as $t) {
                    $targetTitles[] = strtolower($t);
                }
            }
        }

        foreach ($targetTitles as $tTitle) {
            if (str_contains($jobTitle, $tTitle) || str_contains($tTitle, $jobTitle)) {
                $roleScore = 25;
                break;
            }
            $wordsT = array_filter(explode(' ', $tTitle), fn ($w) => strlen($w) > 3);
            $wordsJ = array_filter(explode(' ', $jobTitle), fn ($w) => strlen($w) > 3);
            $commonWords = array_intersect($wordsT, $wordsJ);
            if (count($commonWords) >= 2) {
                $roleScore = max($roleScore, 20);
            } elseif (count($commonWords) === 1) {
                $roleScore = max($roleScore, 12);
            }
        }

        // Employability base readiness (Max 20 points)
        $latestAssessment = $student->assessments->first();
        $employability = (int) ($latestAssessment->employability_score ?? $profile->employability_score ?? 0);
        $readinessScore = ($employability / 100) * 20;

        // Field / Department alignment (Max 10 points)
        $dept = strtolower($job->department ?? '');
        $prodi = strtolower($profile->prodi ?? '');
        $jurusan = strtolower($profile->jurusan ?? '');

        $isTechJob = str_contains($dept, 'software') || str_contains($dept, 'informasi') || str_contains($dept, 'data') || str_contains($dept, 'web') || str_contains($dept, 'cloud') || str_contains($dept, 'mobile') || str_contains($dept, 'it');
        $isHardwareJob = str_contains($dept, 'hardware') || str_contains($dept, 'iot') || str_contains($dept, 'embedded') || str_contains($dept, 'elektro') || str_contains($dept, 'automation');

        if ($isTechJob && (str_contains($prodi, 'rekayasa perangkat lunak') || str_contains($prodi, 'komputer') || str_contains($prodi, 'informatika') || str_contains($jurusan, 'ti'))) {
            $fieldScore = 10;
        } elseif ($isHardwareJob && (str_contains($prodi, 'elektronika') || str_contains($prodi, 'listrik') || str_contains($prodi, 'telekomunikasi') || str_contains($jurusan, 'elektro'))) {
            $fieldScore = 10;
        } elseif ($prodi && str_contains($dept, $prodi)) {
            $fieldScore = 10;
        } else {
            $fieldScore = 5;
        }

        $totalScore = (int) round($skillScore + $roleScore + $readinessScore + $fieldScore);
        $totalScore = max(15, min(99, $totalScore));

        return [
            'job_id' => $job->id,
            'job_title' => $job->title,
            'job_department' => $job->department,
            'match_score' => $totalScore,
            'matched_skills' => $matchedSkills,
            'missing_skills' => $missingSkills,
            'matched_skills_count' => count($matchedSkills),
            'total_required_skills' => $totalReqSkills,
        ];
    }

    /**
     * Find best matching open job for a student among a collection of open jobs.
     *
     * @param  Collection<int, JobListing>  $openJobs
     * @return array<string, mixed>|null
     */
    public function findBestJobMatch(User $student, $openJobs): ?array
    {
        if ($openJobs->isEmpty()) {
            return null;
        }

        $bestMatch = null;
        foreach ($openJobs as $job) {
            $match = $this->calculateCandidateJobMatch($student, $job);
            if (! $bestMatch || $match['match_score'] > $bestMatch['match_score']) {
                $bestMatch = $match;
            }
        }

        return $bestMatch;
    }

    /**
     * Tampilkan riwayat log aktivitas mitra.
     */
    public function activityLogs(Request $request): View
    {
        $query = Auth::user()->activityLogs()->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Auth::user()->activityLogs()->count(),
            'interviews' => Auth::user()->activityLogs()->where('action', 'like', '%interview%')->count(),
            'accepted' => Auth::user()->activityLogs()->where('action', 'like', '%accepted%')->count(),
            'jobs' => Auth::user()->activityLogs()->where('action', 'like', '%job%')->count(),
        ];

        return view('mitra.activity_logs', compact('logs', 'stats'));
    }
}
