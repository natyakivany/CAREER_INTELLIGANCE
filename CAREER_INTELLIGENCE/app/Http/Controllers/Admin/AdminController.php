<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\MitraNotificationMail;
use App\Mail\MitraWelcomeMail;
use App\Mail\StudentNotificationMail;
use App\Mail\StudentWelcomeMail;
use App\Models\ActivityLog;
use App\Models\Assessment;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Occupation;
use App\Models\OccupationSkill;
use App\Models\PasswordResetRequest;
use App\Models\PendingProfileUpdate;
use App\Models\Portfolio;
use App\Models\StudentProfile;
use App\Models\User;
use App\Notifications\AdminMessageNotification;
use App\Notifications\JobApprovalStatusNotification;
use App\Notifications\ProfileUpdateStatusNotification;
use App\Services\GeminiAssessmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin institutional overview dashboard.
     */
    public function dashboard(): View
    {
        $totalStudents = User::where('role', 'mahasiswa')->count();
        $totalMitra = User::where('role', 'mitra')->count();
        $totalPortfolios = Portfolio::count();
        $totalAssessments = Assessment::where('status', 'completed')->count();

        $avgScore = round((float) (StudentProfile::whereHas('user', function ($q) {
            $q->where('role', 'mahasiswa');
        })->where('employability_score', '>', 0)->avg('employability_score') ?? 0));

        $totalIt = StudentProfile::whereHas('user', function ($q) {
            $q->where('role', 'mahasiswa');
        })->where('target_field', 'it')->count();
        $totalElektro = StudentProfile::whereHas('user', function ($q) {
            $q->where('role', 'mahasiswa');
        })->where('target_field', 'elektro')->count();

        $recentStudents = User::where('role', 'mahasiswa')
            ->with(['studentProfile.targetOccupation', 'portfolios'])
            ->latest()
            ->take(6)
            ->get();

        $recentMitra = User::where('role', 'mitra')
            ->latest()
            ->take(5)
            ->get();

        $recentAssessments = Assessment::with('user')
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        $recentActivityLogs = ActivityLog::with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalMitra',
            'totalPortfolios',
            'totalAssessments',
            'avgScore',
            'totalIt',
            'totalElektro',
            'recentStudents',
            'recentMitra',
            'recentAssessments',
            'recentActivityLogs'
        ));
    }

    /**
     * Display the student management listing.
     */
    public function students(Request $request): View
    {
        $query = User::where('role', 'mahasiswa')
            ->with(['studentProfile.targetOccupation', 'portfolios', 'assessments']);

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($request->filled('field') && in_array($request->input('field'), ['it', 'elektro'], true)) {
            $field = $request->input('field');
            $query->whereHas('studentProfile', function ($q) use ($field): void {
                $q->where('target_field', $field);
            });
        }

        if ($request->filled('tingkat') && in_array($request->input('tingkat'), ['mahasiswa', 'smk'], true)) {
            $tingkat = $request->input('tingkat');
            if ($tingkat === 'smk') {
                $query->whereHas('studentProfile', fn ($q) => $q->smk());
            } else {
                $query->whereHas('studentProfile', fn ($q) => $q->mahasiswa());
            }
        }

        if ($request->input('sort') === 'score_desc') {
            $query->leftJoin('student_profiles', 'users.id', '=', 'student_profiles.user_id')
                ->orderBy('student_profiles.employability_score', 'desc')
                ->select('users.*');
        } else {
            $query->latest();
        }

        $students = $query->paginate(10)->withQueryString();
        $occupations = Occupation::all(['id', 'title', 'field']);

        return view('admin.students', compact('students', 'occupations'));
    }

    /**
     * Show full profile of a student.
     */
    public function showStudent(User $user): View
    {
        if ($user->role !== 'mahasiswa') {
            abort(404, 'Mahasiswa tidak ditemukan.');
        }

        $user->load(['studentProfile.targetOccupation', 'portfolios', 'assessments' => fn ($q) => $q->where('status', 'completed')->latest()]);

        $profile = $user->studentProfile;
        $latestAssessment = $user->assessments->first();

        // Extract top skills manually as AdminController does not have extractTopSkills helper
        $topSkills = [];
        if ($latestAssessment && $latestAssessment->ai_strengths) {
            $topSkills = array_map('trim', explode(',', $latestAssessment->ai_strengths));
            $topSkills = array_slice($topSkills, 0, 3);
        }

        return view('admin.students_show', [
            'student' => $user,
            'profile' => $profile,
            'latestAssessment' => $latestAssessment,
            'topSkills' => $topSkills,
        ]);
    }

    /**
     * Show CV of a student
     */
    public function studentCv(User $user): View|RedirectResponse
    {
        if ($user->role !== 'mahasiswa') {
            abort(404, 'Mahasiswa tidak ditemukan.');
        }

        $user->load(['studentProfile.targetOccupation', 'portfolios', 'assessments' => fn ($q) => $q->where('status', 'completed')->latest()]);

        $profile = $user->studentProfile;

        if (! $profile?->isCvFilled()) {
            return redirect()->route('admin.students.show', $user->id)
                ->with('error', 'Dokumen CV ATS mahasiswa ini belum aktif karena data CV belum diisi oleh mahasiswa.');
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
     * Send notification email to a student from institutional admin.
     */
    public function sendEmailToStudent(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'mahasiswa') {
            abort(404, 'Mahasiswa tidak ditemukan.');
        }

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $adminUser = Auth::user();
        $adminEmail = $adminUser?->email;
        $adminName = $adminUser?->name ?? 'Administrator';
        $isRealAdminEmail = $adminEmail && filter_var($adminEmail, FILTER_VALIDATE_EMAIL) && $adminEmail !== 'admin@gmail.com' && $adminEmail !== $user->email;

        // 1. Selalu kirim notifikasi in-app ke portal mahasiswa
        try {
            $user->notify(new AdminMessageNotification($validated['subject'], $validated['message'], $adminName));
        } catch (\Throwable $e) {
            Log::warning('Failed to send in-app notification to student: '.$e->getMessage());
        }

        // 2. Kirim email melalui Mailer SMTP
        try {
            $mail = new StudentNotificationMail($validated['subject'], $validated['message'], $user->name);
            if ($isRealAdminEmail) {
                $mail->cc($adminEmail);
            }
            Mail::to($user->email)->send($mail);

            $statusMsg = 'Pesan & email resmi berhasil dikirimkan langsung ke '.$user->name.' ('.$user->email.').';
            if ($isRealAdminEmail) {
                $statusMsg .= ' Tembusan telah dikirim ke '.$adminEmail.'.';
            }

            ActivityLog::record(
                action: 'email_sent_to_student',
                title: 'Kirim Email ke Mahasiswa',
                description: "Mengirim email resmi ke mahasiswa {$user->name} ({$user->email}): '{$validated['subject']}'",
                subject: $user,
                properties: ['email' => $user->email, 'subject' => $validated['subject']]
            );

            return back()->with('status', $statusMsg);
        } catch (\Exception $e) {
            Log::warning('SMTP send to student failed: '.$e->getMessage());

            try {
                $mail = new StudentNotificationMail($validated['subject'], $validated['message'], $user->name);
                if ($isRealAdminEmail) {
                    $mail->cc($adminEmail);
                }
                Mail::mailer('log')->to($user->email)->send($mail);
            } catch (\Exception $fallbackEx) {
                // Log fallback
            }

            ActivityLog::record(
                action: 'email_sent_to_student',
                title: 'Kirim Email ke Mahasiswa',
                description: "Mengirim email resmi ke mahasiswa {$user->name} ({$user->email}): '{$validated['subject']}'",
                subject: $user,
                properties: ['email' => $user->email, 'subject' => $validated['subject']]
            );

            return back()->with('status', "Pesan berhasil terkirim ke notifikasi portal {$user->name}! (Catatan: Pengiriman email fisik ke inbox Gmail belum terkirim karena Sandi Aplikasi Google di sistem perlu diperbarui. Anda juga dapat menggunakan tombol 'Buka di Gmail Web' untuk mengirim langsung dari Gmail Anda).");
        }
    }

    /**
     * Store a new student user manually.
     */
    public function storeStudent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nim' => ['nullable', 'string', 'max:50', 'unique:users,nim'],
            'prodi' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'semester' => ['nullable', 'string', 'max:50'],
            'target_field' => ['required', 'in:it,elektro'],
            'ipk' => ['nullable', 'numeric', 'between:0,4.00'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $plainPassword = ($validated['password'] ?? null) ?: 'password';
        $nim = ! empty($validated['nim']) ? trim((string) $validated['nim']) : null;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim' => $nim,
            'role' => 'mahasiswa',
            'password' => $plainPassword,
            'email_verified_at' => now(),
        ]);

        StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => ($validated['prodi'] ?? null) ?: 'Teknik Komputer',
            'jurusan' => ($validated['jurusan'] ?? null) ?: ($validated['target_field'] === 'elektro' ? 'Teknik Elektro' : 'Teknologi Informasi'),
            'semester' => ($validated['semester'] ?? null) ?: 'Semester 7',
            'target_field' => $validated['target_field'],
            'ipk' => $validated['ipk'] ?? 3.50,
            'employability_score' => 0,
        ]);

        // Kirim email selamat datang dan kredensial login ke email pengguna
        try {
            Mail::to($user->email)->send(new StudentWelcomeMail($user, $plainPassword));
            $emailInfo = " dan rincian login telah dikirimkan ke email {$user->email}";
        } catch (\Exception $e) {
            Log::warning('Failed to send student welcome email: '.$e->getMessage());
            $emailInfo = ', namun pengiriman email mengalami kendala (periksa konfigurasi SMTP)';
        }

        ActivityLog::record(
            action: 'student_created',
            title: 'Registrasi Mahasiswa Baru',
            description: "Menambahkan akun mahasiswa baru: {$user->name} (NIM: ".($user->nim ?? '-').')',
            subject: $user,
            properties: ['name' => $user->name, 'email' => $user->email, 'nim' => $user->nim]
        );

        return back()->with('status', "Akun '{$user->name}' berhasil ditambahkan{$emailInfo}.");
    }

    /**
     * Update an existing student's credentials and profile.
     */
    public function updateStudent(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nim' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'prodi' => ['nullable', 'string', 'max:255'],
            'semester' => ['nullable', 'string', 'max:50'],
            'target_field' => ['required', 'in:it,elektro'],
            'ipk' => ['nullable', 'numeric', 'between:0,4.00'],
        ]);

        $nim = ! empty($validated['nim']) ? trim((string) $validated['nim']) : null;

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim' => $nim,
        ]);

        StudentProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'prodi' => $validated['prodi'] ?: 'Teknik Komputer',
                'semester' => $validated['semester'] ?: 'Semester 7',
                'target_field' => $validated['target_field'],
                'ipk' => $validated['ipk'] ?? 3.50,
            ]
        );

        ActivityLog::record(
            action: 'student_updated',
            title: 'Pembaruan Data Mahasiswa',
            description: "Memperbarui data akun & profil akademik mahasiswa: {$user->name}",
            subject: $user,
            properties: ['name' => $user->name, 'email' => $user->email, 'nim' => $user->nim]
        );

        return back()->with('status', "Data '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Display the industry partners management listing.
     */
    public function mitra(Request $request): View
    {
        $query = User::where('role', 'mitra');

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->input('sort') === 'accepted_desc') {
            $query->withCount(['jobListings as accepted_students_count' => function ($query) {
                $query->join('job_applications', 'job_listings.id', '=', 'job_applications.job_listing_id')
                    ->where('job_applications.status', 'Diterima');
            }])->orderByDesc('accepted_students_count');
        } else {
            $query->latest();
        }

        $mitraList = $query->paginate(10)->withQueryString();

        return view('admin.mitra', compact('mitraList'));
    }

    /**
     * Show full profile of an industry partner.
     */
    public function showMitra(User $user): View
    {
        if ($user->role !== 'mitra') {
            abort(404, 'Mitra tidak ditemukan.');
        }

        $user->load(['jobListings.applications']);

        $jobs = $user->jobListings()->latest()->get();
        $totalJobs = $jobs->count();
        $activeJobs = $jobs->where('status', 'Buka')->count();
        $totalApplications = $jobs->sum(fn ($job) => $job->applications->count());

        return view('admin.mitra_show', compact('user', 'jobs', 'totalJobs', 'activeJobs', 'totalApplications'));
    }

    /**
     * Send email to industry partner.
     */
    public function sendEmailToMitra(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'mitra') {
            abort(404, 'Mitra tidak ditemukan.');
        }

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $adminUser = Auth::user();
        $adminName = $adminUser?->name ?? 'Administrator';

        // 1. Selalu kirim notifikasi in-app ke portal mitra
        try {
            $user->notify(new AdminMessageNotification($validated['subject'], $validated['message'], $adminName));
        } catch (\Throwable $e) {
            Log::warning('Failed to send in-app notification to mitra: '.$e->getMessage());
        }

        // 2. Kirim email via Mailer SMTP
        try {
            Mail::to($user->email)->send(
                new MitraNotificationMail($validated['subject'], $validated['message'])
            );

            ActivityLog::record(
                action: 'email_sent_to_mitra',
                title: 'Kirim Email ke Mitra',
                description: "Mengirim email resmi ke mitra industri {$user->name} ({$user->email}): '{$validated['subject']}'",
                subject: $user,
                properties: ['email' => $user->email, 'subject' => $validated['subject']]
            );

            return back()->with('status', 'Pesan & email resmi berhasil dikirim ke mitra '.$user->name);
        } catch (\Exception $e) {
            Log::warning('SMTP send to mitra failed, falling back to log: '.$e->getMessage());

            try {
                Mail::mailer('log')->to($user->email)->send(
                    new MitraNotificationMail($validated['subject'], $validated['message'])
                );
            } catch (\Exception $fallbackEx) {
                // Log fallback
            }

            ActivityLog::record(
                action: 'email_sent_to_mitra',
                title: 'Kirim Email ke Mitra',
                description: "Mengirim email resmi ke mitra industri {$user->name} ({$user->email}): '{$validated['subject']}'",
                subject: $user,
                properties: ['email' => $user->email, 'subject' => $validated['subject']]
            );

            return back()->with('status', "Pesan berhasil terkirim ke notifikasi portal mitra {$user->name}! (Catatan: Pengiriman email fisik ke inbox Gmail belum terkirim karena Sandi Aplikasi Google di sistem perlu diperbarui. Anda juga dapat menggunakan tombol 'Buka di Gmail Web' untuk mengirim langsung dari Gmail Anda).");
        }
    }

    /**
     * Store a new industry partner account.
     */
    public function storeMitra(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $plainPassword = ($validated['password'] ?? null) ?: 'password';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => 'mitra',
            'password' => Hash::make($plainPassword),
            'email_verified_at' => now(),
        ]);

        // Send welcome email with login credentials
        try {
            Mail::to($user->email)
                ->send(new MitraWelcomeMail($user, $plainPassword));
        } catch (\Exception $e) {
            Log::warning('Failed to send mitra welcome email: '.$e->getMessage());
        }

        ActivityLog::record(
            action: 'mitra_created',
            title: 'Pendaftaran Mitra Baru',
            description: "Mendaftarkan akun mitra industri baru: {$user->name} ({$user->email})",
            subject: $user,
            properties: ['name' => $user->name, 'email' => $user->email]
        );

        return back()->with('status', "Mitra industri '{$user->name}' berhasil didaftarkan. Email selamat datang telah dikirim ke {$user->email}.");
    }

    /**
     * Reset a user's password to default 'password'.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        $user->password = Hash::make('');
        $user->save();

        // Jika sebelumnya ada request minta reset, tandai selesai
        PasswordResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'completed']);

        ActivityLog::record(
            action: 'password_reset',
            title: 'Reset Kata Sandi Akun',
            description: "Mereset kata sandi akun pengguna: {$user->name} ({$user->email})",
            subject: $user
        );

        return back()->with('status', "Kata sandi untuk '{$user->name}' berhasil direset menjadi KOSONG (tanpa sandi).");
    }

    /**
     * Display the industry standard occupations list.
     */
    public function occupations(): View
    {
        $occupations = Occupation::with('skills')->latest()->get();

        return view('admin.occupations', compact('occupations'));
    }

    /**
     * Store a new occupation standard.
     */
    public function storeOccupation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'field' => ['required', 'in:it,elektro'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'average_salary' => ['nullable', 'string', 'max:100'],
            'demand_level' => ['required', 'in:Tinggi,Sangat Tinggi,Menengah'],
            'skills' => ['nullable', 'string'], // comma-separated initial skills
        ]);

        $occupation = Occupation::create([
            'title' => $validated['title'],
            'field' => $validated['field'],
            'category' => ($validated['category'] ?? null) ?: ($validated['field'] === 'elektro' ? 'Teknik Elektro' : 'Software Engineering'),
            'description' => $validated['description'] ?? null,
            'average_salary' => ($validated['average_salary'] ?? null) ?: 'Rp 8.000.000 - Rp 15.000.000',
            'demand_level' => $validated['demand_level'],
        ]);

        if (! empty($validated['skills'])) {
            $skillNames = array_map('trim', explode(',', $validated['skills']));
            foreach ($skillNames as $sName) {
                if ($sName !== '') {
                    OccupationSkill::create([
                        'occupation_id' => $occupation->id,
                        'skill_name' => $sName,
                        'benchmark_score' => 85,
                        'is_core' => true,
                    ]);
                }
            }
        }

        return back()->with('status', "Standar profesi baru '{$occupation->title}' berhasil ditambahkan.");
    }

    /**
     * Generate an occupation recommendation using Gemini AI.
     */
    public function generateOccupationAi(Request $request, GeminiAssessmentService $gemini): RedirectResponse
    {
        $field = $request->input('field', 'it');

        try {
            $data = $gemini->generateOccupationRecommendation($field);

            $occupation = Occupation::create([
                'title' => $data['title'],
                'field' => $field,
                'category' => $data['category'] ?? null,
                'description' => $data['description'] ?? null,
                'average_salary' => $data['average_salary'] ?? null,
                'demand_level' => in_array($data['demand_level'] ?? '', ['Tinggi', 'Sangat Tinggi', 'Menengah']) ? $data['demand_level'] : 'Tinggi',
            ]);

            if (! empty($data['skills']) && is_array($data['skills'])) {
                foreach ($data['skills'] as $skill) {
                    OccupationSkill::create([
                        'occupation_id' => $occupation->id,
                        'skill_name' => $skill['name'],
                        'benchmark_score' => $skill['score'] ?? 80,
                        'is_core' => true,
                    ]);
                }
            }

            return back()->with('status', "AI Rekomendasi Profesi '{$occupation->title}' berhasil dibuat!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghasilkan rekomendasi dari AI: '.$e->getMessage());
        }
    }

    /**
     * Update an existing occupation standard.
     */
    public function updateOccupation(Request $request, Occupation $occupation): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'field' => ['required', 'in:it,elektro'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'average_salary' => ['nullable', 'string', 'max:100'],
            'demand_level' => ['required', 'in:Tinggi,Sangat Tinggi,Menengah'],
        ]);

        $occupation->update($validated);

        return back()->with('status', "Standar profesi '{$occupation->title}' berhasil diperbarui.");
    }

    /**
     * Delete an occupation standard.
     */
    public function destroyOccupation(Occupation $occupation): RedirectResponse
    {
        $title = $occupation->title;
        $occupation->delete();

        return back()->with('status', "Standar profesi '{$title}' berhasil dihapus.");
    }

    /**
     * Add a benchmark skill to an occupation.
     */
    public function addSkillToOccupation(Request $request, Occupation $occupation): RedirectResponse
    {
        $validated = $request->validate([
            'skill_name' => ['required', 'string', 'max:255'],
            'benchmark_score' => ['required', 'integer', 'between:50,100'],
            'is_core' => ['nullable', 'boolean'],
        ]);

        $skill = OccupationSkill::create([
            'occupation_id' => $occupation->id,
            'skill_name' => $validated['skill_name'],
            'benchmark_score' => $validated['benchmark_score'],
            'is_core' => $request->boolean('is_core', true),
        ]);

        return back()->with('status', "Keahlian benchmark '{$skill->skill_name}' berhasil ditambahkan ke '{$occupation->title}'.");
    }

    /**
     * Delete an individual benchmark skill.
     */
    public function destroyOccupationSkill(OccupationSkill $skill): RedirectResponse
    {
        $name = $skill->skill_name;
        $skill->delete();

        return back()->with('status', "Benchmark keahlian '{$name}' berhasil dihapus.");
    }

    /**
     * Display the assessment supervision view.
     */
    public function assessments(Request $request): View
    {
        $query = Assessment::with(['user.studentProfile', 'questions']);

        if ($request->filled('field') && in_array($request->input('field'), ['it', 'elektro', 'all'], true)) {
            $query->where('field', $request->input('field'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->whereHas('user', function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $assessments = $query->latest()->paginate(10)->withQueryString();

        return view('admin.assessments', compact('assessments'));
    }

    /**
     * Reset / delete a student's assessment session.
     */
    public function destroyAssessment(Assessment $assessment): RedirectResponse
    {
        $studentName = $assessment->user?->name ?? 'Mahasiswa';
        $assessment->delete();

        return back()->with('status', "Sesi asesmen untuk '{$studentName}' berhasil direset/dihapus.");
    }

    /**
     * Display printable institutional accreditation report.
     */
    public function printReport(Request $request): View
    {
        $query = User::where('role', 'mahasiswa')
            ->with(['studentProfile.targetOccupation', 'portfolios', 'assessments']);

        if ($request->filled('jurusan') && $request->jurusan !== 'Semua Jurusan' && $request->jurusan !== 'all') {
            $query->whereHas('studentProfile', fn ($q) => $q->where('jurusan', $request->jurusan));
        }

        if ($request->filled('asal_sekolah') && $request->asal_sekolah !== 'Semua Sekolah' && $request->asal_sekolah !== 'all') {
            $query->whereHas('studentProfile', fn ($q) => $q->where('high_school_name', $request->asal_sekolah));
        }

        if ($request->filled('tingkat') && $request->tingkat !== 'all') {
            if ($request->tingkat === 'SMK') {
                $query->whereHas('studentProfile', fn ($q) => $q->smk());
            } elseif ($request->tingkat === 'Mahasiswa') {
                $query->whereHas('studentProfile', fn ($q) => $q->mahasiswa());
            } elseif ($request->tingkat === 'Alumni') {
                $query->whereHas('studentProfile', fn ($q) => $q->whereIn('semester', ['Alumni / Fresh Graduate PT', 'Lulusan SMK/SMA (Siap Kerja)']));
            }
        }

        $students = $query->get();

        $totalStudents = $students->count();
        $totalMitra = User::where('role', 'mitra')->count();

        // Sum portfolios for these students
        $totalPortfolios = $students->sum(fn ($s) => $s->portfolios->count());

        // Count assessments for these students
        $totalAssessments = $students->sum(fn ($s) => $s->assessments->where('status', 'completed')->count());

        // Avg Employability Score for these students
        $scores = $students->map(fn ($s) => $s->studentProfile?->employability_score)->filter(fn ($score) => $score > 0);
        $avgScore = $scores->count() > 0 ? round($scores->average()) : 0;

        // Distinct list of majors for the filters
        $jurusanOptions = StudentProfile::whereNotNull('jurusan')
            ->where('jurusan', '!=', '')
            ->distinct()
            ->pluck('jurusan');

        // Distinct list of high schools for the filters
        $sekolahOptions = StudentProfile::whereNotNull('high_school_name')
            ->where('high_school_name', '!=', '')
            ->distinct()
            ->pluck('high_school_name');

        return view('admin.print_report', compact(
            'students',
            'totalStudents',
            'totalMitra',
            'totalPortfolios',
            'totalAssessments',
            'avgScore',
            'jurusanOptions',
            'sekolahOptions'
        ));
    }

    /**
     * Remove the specified user resource.
     */
    public function destroyUser(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun administrator Anda sendiri.');
        }

        $role = $user->role;
        $userName = $user->name;
        $user->delete();

        $label = $role === 'mitra' ? 'Mitra Industri' : ($role === 'mahasiswa' ? 'Mahasiswa' : 'Pengguna');

        ActivityLog::record(
            action: 'user_deleted',
            title: 'Penghapusan Akun Pengguna',
            description: "Menghapus akun {$label}: {$userName}",
            properties: ['deleted_user_name' => $userName, 'deleted_user_role' => $role]
        );

        return back()->with('status', "Data {$label} '{$userName}' berhasil dihapus dari sistem.");
    }

    public function leaderboards(): View
    {
        // Get Mitra with highest number of accepted students, active listings, and applications
        $topMitra = User::where('role', 'mitra')
            ->withCount([
                'jobListings as accepted_students_count' => function ($query) {
                    $query->join('job_applications', 'job_listings.id', '=', 'job_applications.job_listing_id')
                        ->where('job_applications.status', 'Diterima');
                },
                'jobListings as active_jobs_count' => function ($query) {
                    $query->where('status', 'Buka');
                },
                'jobListings as total_applications_count' => function ($query) {
                    $query->join('job_applications', 'job_listings.id', '=', 'job_applications.job_listing_id');
                },
            ])
            ->orderByDesc('accepted_students_count')
            ->orderByDesc('active_jobs_count')
            ->orderByDesc('total_applications_count')
            ->get();

        // Base query for students with valid employability score
        $baseStudentQuery = StudentProfile::with('user')
            ->whereHas('user', function ($q) {
                $q->where('role', 'mahasiswa');
            })
            ->where('employability_score', '>', 0)
            ->orderByDesc('employability_score');

        // All students (Combined)
        $topStudents = (clone $baseStudentQuery)->get();

        // Distinct rankings for Mahasiswa (Perguruan Tinggi) vs SMK
        $topMahasiswa = (clone $baseStudentQuery)->mahasiswa()->get();
        $topSmk = (clone $baseStudentQuery)->smk()->get();

        // Get Most Popular Fields (Departments with most applications)
        $popularFields = JobListing::join('job_applications', 'job_listings.id', '=', 'job_applications.job_listing_id')
            ->select('job_listings.department', DB::raw('count(job_applications.id) as applications_count'))
            ->groupBy('job_listings.department')
            ->orderByDesc('applications_count')
            ->get();

        // Overall summary statistics for KPI widgets
        $totalAcceptedStudents = JobApplication::where('status', 'Diterima')->count();
        $totalApplications = JobApplication::count();
        $avgTopScore = $topStudents->isNotEmpty() ? round($topStudents->avg('employability_score'), 1) : 0;
        $highestScoreStudent = $topStudents->first();
        $highestScoreMahasiswa = $topMahasiswa->first();
        $highestScoreSmk = $topSmk->first();
        $avgScoreMahasiswa = $topMahasiswa->isNotEmpty() ? round($topMahasiswa->avg('employability_score'), 1) : 0;
        $avgScoreSmk = $topSmk->isNotEmpty() ? round($topSmk->avg('employability_score'), 1) : 0;
        $topHiringMitra = $topMitra->first();
        $totalOpenJobs = JobListing::where('status', 'Buka')->count();

        return view('admin.leaderboards', compact(
            'topMitra',
            'topStudents',
            'topMahasiswa',
            'topSmk',
            'popularFields',
            'totalAcceptedStudents',
            'totalApplications',
            'avgTopScore',
            'highestScoreStudent',
            'highestScoreMahasiswa',
            'highestScoreSmk',
            'avgScoreMahasiswa',
            'avgScoreSmk',
            'topHiringMitra',
            'totalOpenJobs'
        ));
    }

    public function approvals(): View
    {
        if (Auth::check() && Auth::user()) {
            Auth::user()->unreadNotifications->markAsRead();
            Auth::user()->unsetRelation('unreadNotifications');
        }

        $pendingJobs = JobListing::with('user')->where('status', 'Menunggu Persetujuan')->get();
        $pendingProfiles = PendingProfileUpdate::with('user')->where('status', 'pending')->get();

        return view('admin.approvals', compact('pendingJobs', 'pendingProfiles'));
    }

    public function approveJob(JobListing $job): RedirectResponse
    {
        $job->update(['status' => 'Buka']);

        try {
            $job->user?->notify(new JobApprovalStatusNotification($job, 'approved'));
        } catch (\Throwable $e) {
            Log::warning('Failed to notify mitra of job approval: '.$e->getMessage());
        }

        ActivityLog::record(
            action: 'job_approved',
            title: 'Persetujuan Lowongan',
            description: "Menyetujui publikasi lowongan '{$job->title}' dari mitra {$job->user->name}",
            subject: $job,
            properties: ['job_title' => $job->title, 'mitra_name' => $job->user->name]
        );

        return back()->with('status', "Lowongan '{$job->title}' berhasil disetujui dan kini dipublikasikan.");
    }

    public function rejectJob(JobListing $job): RedirectResponse
    {
        $job->update(['status' => 'Tutup']);

        try {
            $job->user?->notify(new JobApprovalStatusNotification($job, 'rejected'));
        } catch (\Throwable $e) {
            Log::warning('Failed to notify mitra of job rejection: '.$e->getMessage());
        }

        ActivityLog::record(
            action: 'job_rejected',
            title: 'Penolakan Lowongan',
            description: "Menolak publikasi lowongan '{$job->title}' dari mitra {$job->user->name}",
            subject: $job,
            properties: ['job_title' => $job->title, 'mitra_name' => $job->user->name]
        );

        return back()->with('status', "Lowongan '{$job->title}' ditolak.");
    }

    public function approveProfileUpdate(PendingProfileUpdate $update): RedirectResponse
    {
        $update->user->update([
            'name' => $update->name,
            'email' => $update->email,
        ]);
        $update->update(['status' => 'approved']);

        try {
            $update->user?->notify(new ProfileUpdateStatusNotification($update, 'approved'));
        } catch (\Throwable $e) {
            Log::warning('Failed to notify mitra of profile update approval: '.$e->getMessage());
        }

        ActivityLog::record(
            action: 'profile_update_approved',
            title: 'Persetujuan Pembaruan Profil',
            description: "Menyetujui permohonan perubahan profil mitra '{$update->user->name}'",
            subject: $update
        );

        return back()->with('status', "Perubahan profil mitra '{$update->user->name}' berhasil disetujui.");
    }

    public function rejectProfileUpdate(PendingProfileUpdate $update): RedirectResponse
    {
        $update->update(['status' => 'rejected']);

        try {
            $update->user?->notify(new ProfileUpdateStatusNotification($update, 'rejected'));
        } catch (\Throwable $e) {
            Log::warning('Failed to notify mitra of profile update rejection: '.$e->getMessage());
        }

        ActivityLog::record(
            action: 'profile_update_rejected',
            title: 'Penolakan Pembaruan Profil',
            description: "Menolak permohonan perubahan profil mitra '{$update->user->name}'",
            subject: $update
        );

        return back()->with('status', "Perubahan profil mitra '{$update->user->name}' ditolak.");
    }

    /**
     * Tampilkan riwayat log aktivitas seluruh sistem (Admin, Mitra, dll).
     */
    public function activityLogs(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'mitra' => ActivityLog::where('role', 'mitra')->count(),
            'admin' => ActivityLog::where('role', 'admin')->count(),
        ];

        return view('admin.activity_logs', compact('logs', 'stats'));
    }
}
