<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // 1. Dashboard Utama Mahasiswa
    Route::get('/dashboard', [CareerController::class, 'dashboard'])->name('dashboard');

    // 2. Modul Profil Mahasiswa
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/recommend-occupations', [ProfileController::class, 'recommendOccupations'])->name('profile.recommend-occupations');

    // 3. Modul Portofolio
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
    Route::post('/portfolio', [PortfolioController::class, 'store'])->name('portfolio.store');
    Route::delete('/portfolio/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolio.destroy');

    // 4. Modul Skill Assessment Gemini AI
    Route::get('/assessment', [AssessmentController::class, 'index'])->name('assessment');
    Route::get('/assessment/history', [AssessmentController::class, 'history'])->name('assessment.history');
    Route::post('/assessment/start', [AssessmentController::class, 'start'])->name('assessment.start');
    Route::get('/assessment/{assessment}', [AssessmentController::class, 'show'])->name('assessment.show');
    Route::post('/assessment/{assessment}/submit', [AssessmentController::class, 'submit'])->name('assessment.submit');
    Route::get('/assessment/{assessment}/result', [AssessmentController::class, 'result'])->name('assessment.result');

    // 5. Modul Skill Gap Analysis
    Route::get('/skill-gap', [CareerController::class, 'skillGap'])->name('skill-gap');

    // 6. Modul Rekomendasi Karier
    Route::get('/recommendation', [CareerController::class, 'recommendation'])->name('recommendation');

    // 7. Modul Career Roadmap
    Route::get('/roadmap', [CareerController::class, 'roadmap'])->name('roadmap');
    Route::post('/roadmap/{roadmap}/toggle', [CareerController::class, 'toggleRoadmapItem'])->name('roadmap.toggle');

    // 8. Modul CV Builder ATS Berbasis Kompetensi
    Route::get('/cv-builder', [CvController::class, 'index'])->name('cv-builder');
    Route::post('/cv-builder', [CvController::class, 'update'])->name('cv-builder.update');
    Route::post('/cv-builder/ai-generate', [CvController::class, 'generateAi'])->name('cv-builder.ai-generate');
    Route::get('/cv-builder/print', [CvController::class, 'print'])->name('cv-builder.print');

    Route::get('/jobs', [CareerController::class, 'jobs'])->name('jobs');
    Route::get('/jobs/{job}', [CareerController::class, 'showJob'])->name('jobs.show');
    Route::post('/jobs/{job}/apply', [CareerController::class, 'applyJob'])->name('jobs.apply');
    Route::post('/jobs/{job}/reject', [CareerController::class, 'rejectJob'])->name('jobs.reject');
    Route::get('/applications', [CareerController::class, 'applications'])->name('applications.index');
    Route::patch('/applications/{application}/confirm', [CareerController::class, 'confirmInterview'])->name('applications.confirm');
    Route::get('/applications/{application}/download-letter', [CareerController::class, 'downloadAcceptanceLetter'])->name('applications.download-letter');

    // Notifications
    Route::post('/notifications/read-all', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        session([
            'mitra_viewed_accepted_at' => now(),
            'mitra_viewed_rejected_at' => now(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    })->name('notifications.readAll');

    Route::post('/notifications/{id}/read', function (Request $request, string $id) {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    })->name('notifications.read');

    Route::get('/notifications/{id}/go', function (Request $request, string $id) {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();

            $data = $notification->data ?? [];

            if (! empty($data['url'])) {
                return redirect($data['url']);
            }

            if ($request->user()->role === 'admin') {
                return redirect()->route('admin.approvals');
            } elseif ($request->user()->role === 'mitra') {
                if (isset($data['type']) && str_contains($data['type'], 'job_approval')) {
                    return redirect()->route('mitra.jobs');
                }
                if (isset($data['type']) && str_contains($data['type'], 'profile_update')) {
                    return redirect()->route('profile.edit');
                }
                if (! empty($data['job_id'])) {
                    return redirect()->route('mitra.applications.show', $data['job_id']);
                }

                return redirect()->route('mitra.applications.index');
            } else {
                return redirect()->route('applications.index');
            }
        }

        return back();
    })->name('notifications.go');
});

// Portal Mitra Industri
Route::middleware(['auth', 'verified', 'role:mitra'])->prefix('mitra')->name('mitra.')->group(function () {
    Route::get('/dashboard', [MitraController::class, 'dashboard'])->name('dashboard');
    Route::get('/jobs', [MitraController::class, 'jobs'])->name('jobs');
    Route::post('/jobs', [MitraController::class, 'storeJob'])->name('jobs.store');
    Route::put('/jobs/{job}', [MitraController::class, 'updateJob'])->name('jobs.update');
    Route::delete('/jobs/{job}', [MitraController::class, 'destroyJob'])->name('jobs.destroy');
    Route::get('/applications', [MitraController::class, 'applications'])->name('applications.index');
    Route::get('/applications/{job}', [MitraController::class, 'showJobApplications'])->name('applications.show');
    Route::patch('/applications/{application}/status', [MitraController::class, 'updateApplicationStatus'])->name('applications.status');
    Route::delete('/applications/{application}', [MitraController::class, 'destroyApplication'])->name('applications.destroy');
    Route::get('/applications/{application}/download-letter', [MitraController::class, 'downloadAcceptanceLetter'])->name('applications.download-letter');
    Route::get('/kandidat-diterima', [MitraController::class, 'acceptedCandidates'])->name('candidates.accepted');
    Route::get('/kandidat-ditolak', [MitraController::class, 'rejectedCandidates'])->name('candidates.rejected');
    Route::get('/candidates', [MitraController::class, 'candidates'])->name('candidates');
    Route::get('/candidates/{user}', [MitraController::class, 'showCandidate'])->name('candidates.show');
    Route::get('/candidates/{user}/cv', [MitraController::class, 'candidateCv'])->name('candidates.cv');
    Route::post('/candidates/{user}/contact', [MitraController::class, 'contactCandidate'])->name('candidates.contact');
    Route::get('/profile', [MitraController::class, 'profile'])->name('profile');
    Route::patch('/profile', [MitraController::class, 'updateProfile'])->name('profile.update');
    Route::get('/activity-logs', [MitraController::class, 'activityLogs'])->name('activity-logs');
});

// Portal Administrator Kampus
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');
    Route::get('/leaderboards', [AdminController::class, 'leaderboards'])->name('leaderboards');

    // Manajemen Mahasiswa
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::get('/students/{user}', [AdminController::class, 'showStudent'])->name('students.show');
    Route::get('/students/{user}/cv', [AdminController::class, 'studentCv'])->name('students.cv');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('students.store');
    Route::put('/students/{user}', [AdminController::class, 'updateStudent'])->name('students.update');
    Route::post('/students/{user}/email', [AdminController::class, 'sendEmailToStudent'])->name('students.email');

    // Manajemen Mitra Industri
    Route::get('/mitra', [AdminController::class, 'mitra'])->name('mitra');
    Route::get('/mitra/{user}', [AdminController::class, 'showMitra'])->name('mitra.show');
    Route::post('/mitra/{user}/email', [AdminController::class, 'sendEmailToMitra'])->name('mitra.email');
    Route::post('/mitra', [AdminController::class, 'storeMitra'])->name('mitra.store');

    // Manajemen Pengguna Umum (Hapus Akun & Reset Password)
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');

    // Manajemen Persetujuan (Approval)
    Route::get('/approvals', [AdminController::class, 'approvals'])->name('approvals');
    Route::patch('/jobs/{job}/approve', [AdminController::class, 'approveJob'])->name('jobs.approve');
    Route::patch('/jobs/{job}/reject', [AdminController::class, 'rejectJob'])->name('jobs.reject');
    Route::patch('/profile-updates/{update}/approve', [AdminController::class, 'approveProfileUpdate'])->name('profile-updates.approve');
    Route::patch('/profile-updates/{update}/reject', [AdminController::class, 'rejectProfileUpdate'])->name('profile-updates.reject');

    // Standar Profesi & Benchmark Skills (CRUD)
    Route::get('/occupations', [AdminController::class, 'occupations'])->name('occupations');
    Route::post('/occupations', [AdminController::class, 'storeOccupation'])->name('occupations.store');
    Route::post('/occupations/generate-ai', [AdminController::class, 'generateOccupationAi'])->name('occupations.generate-ai');
    Route::put('/occupations/{occupation}', [AdminController::class, 'updateOccupation'])->name('occupations.update');
    Route::delete('/occupations/{occupation}', [AdminController::class, 'destroyOccupation'])->name('occupations.destroy');
    Route::post('/occupations/{occupation}/skills', [AdminController::class, 'addSkillToOccupation'])->name('occupations.skills.store');
    Route::delete('/occupations/skills/{skill}', [AdminController::class, 'destroyOccupationSkill'])->name('occupations.skills.destroy');

    // Monitoring Asesmen Gemini AI
    Route::get('/assessments', [AdminController::class, 'assessments'])->name('assessments');
    Route::delete('/assessments/{assessment}', [AdminController::class, 'destroyAssessment'])->name('assessments.destroy');

    // Laporan Rekapitulasi Cetak
    Route::get('/reports/print', [AdminController::class, 'printReport'])->name('reports.print');
});

require __DIR__.'/auth.php';
