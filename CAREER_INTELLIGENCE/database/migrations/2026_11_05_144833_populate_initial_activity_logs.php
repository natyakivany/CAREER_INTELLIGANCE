<?php

use App\Models\ActivityLog;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Log dari lowongan pekerjaan yang sudah ada
        $jobs = JobListing::with('user')->get();
        foreach ($jobs as $job) {
            if ($job->user) {
                ActivityLog::create([
                    'user_id' => $job->user_id,
                    'role' => 'mitra',
                    'action' => 'job_created',
                    'title' => 'Membuat Lowongan Baru',
                    'description' => "Membuat lowongan baru '{$job->title}' ({$job->type})",
                    'subject_type' => JobListing::class,
                    'subject_id' => $job->id,
                    'properties' => ['title' => $job->title, 'type' => $job->type, 'location' => $job->location],
                    'created_at' => $job->created_at ?? now(),
                    'updated_at' => $job->created_at ?? now(),
                ]);

                if ($job->status === 'Buka') {
                    $admin = User::where('role', 'admin')->first();
                    ActivityLog::create([
                        'user_id' => $admin?->id,
                        'role' => 'admin',
                        'action' => 'job_approved',
                        'title' => 'Persetujuan Lowongan',
                        'description' => "Menyetujui publikasi lowongan '{$job->title}' dari mitra {$job->user->name}",
                        'subject_type' => JobListing::class,
                        'subject_id' => $job->id,
                        'properties' => ['job_title' => $job->title, 'mitra_name' => $job->user->name],
                        'created_at' => $job->updated_at ?? now(),
                        'updated_at' => $job->updated_at ?? now(),
                    ]);
                }
            }
        }

        // 2. Log dari wawancara dan lamaran yang sudah ada
        $applications = JobApplication::with(['user', 'jobListing.user'])->whereNotNull('interview_date')->get();
        foreach ($applications as $app) {
            $mitra = $app->jobListing?->user;
            if ($mitra) {
                $methodLabel = $app->isOfflineInterview() ? 'Offline (Tatap Muka)' : 'Online (Daring)';
                ActivityLog::create([
                    'user_id' => $mitra->id,
                    'role' => 'mitra',
                    'action' => 'interview_scheduled',
                    'title' => 'Penjadwalan Wawancara',
                    'description' => "Menjadwalkan wawancara {$methodLabel} untuk pelamar {$app->user->name} pada lowongan '{$app->jobListing->title}'",
                    'subject_type' => JobApplication::class,
                    'subject_id' => $app->id,
                    'properties' => [
                        'applicant_name' => $app->user->name,
                        'job_title' => $app->jobListing->title,
                        'interview_type' => $app->interview_type,
                        'interview_date' => $app->interview_date?->format('Y-m-d H:i'),
                    ],
                    'created_at' => $app->updated_at ?? now(),
                    'updated_at' => $app->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation needed
    }
};
