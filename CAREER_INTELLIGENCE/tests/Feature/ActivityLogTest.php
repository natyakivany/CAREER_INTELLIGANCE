<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\User;
use Database\Seeders\OccupationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OccupationSeeder::class);
    }

    public function test_mitra_can_view_their_activity_logs(): void
    {
        $mitra = User::factory()->create(['role' => 'mitra', 'email_verified_at' => now()]);

        ActivityLog::record(
            action: 'job_created',
            title: 'Membuat Lowongan Baru',
            description: 'Membuat lowongan baru Full Stack Engineer',
            user: $mitra
        );

        $response = $this->actingAs($mitra)->get(route('mitra.activity-logs'));

        $response->assertOk();
        $response->assertSee('Log Aktivitas Mitra');
        $response->assertSee('Membuat Lowongan Baru');
        $response->assertSee('Membuat lowongan baru Full Stack Engineer');

        // Mitra dashboard also includes recent activity logs
        $dashResponse = $this->actingAs($mitra)->get(route('mitra.dashboard'));
        $dashResponse->assertOk();
        $dashResponse->assertSee('Log Aktivitas Terbaru');
        $dashResponse->assertSee('Membuat Lowongan Baru');
    }

    public function test_admin_can_view_all_activity_logs_and_filter(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $mitra = User::factory()->create(['role' => 'mitra', 'email_verified_at' => now()]);

        ActivityLog::record(
            action: 'job_created',
            title: 'Membuat Lowongan Baru',
            description: 'Lowongan Mitra Golang Dev',
            user: $mitra
        );

        ActivityLog::record(
            action: 'job_approved',
            title: 'Persetujuan Lowongan',
            description: 'Menyetujui lowongan Golang Dev',
            user: $admin
        );

        $response = $this->actingAs($admin)->get(route('admin.activity-logs'));
        $response->assertOk();
        $response->assertSee('Log Aktivitas Sistem');
        $response->assertSee('Membuat Lowongan Baru');
        $response->assertSee('Persetujuan Lowongan');

        // Filter by role
        $mitraFilterResponse = $this->actingAs($admin)->get(route('admin.activity-logs', ['role' => 'mitra']));
        $mitraFilterResponse->assertOk();
        $mitraFilterResponse->assertSee('Membuat Lowongan Baru');

        // Admin dashboard includes recent activity logs
        $dashResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashResponse->assertOk();
        $dashResponse->assertSee('Log Aktivitas Sistem Terkini');
    }

    public function test_mitra_scheduling_interview_creates_activity_log(): void
    {
        Notification::fake();
        Mail::fake();

        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'PT Mitra Log', 'email_verified_at' => now()]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Doni Santoso', 'email_verified_at' => now()]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Web Developer',
            'department' => 'IT',
            'type' => 'Full-time',
            'location' => 'Padang',
            'duration' => '6 Bulan',
            'status' => 'Buka',
        ]);

        $application = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Menunggu Review',
            'match_score' => 90,
        ]);

        $this->actingAs($mitra)->patch(route('mitra.applications.status', $application), [
            'status' => 'Wawancara',
            'interview_type' => 'offline',
            'interview_location' => 'Ruang Meeting 2',
            'interview_date' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $mitra->id,
            'role' => 'mitra',
            'action' => 'interview_scheduled',
            'title' => 'Penjadwalan Wawancara',
        ]);
    }

    public function test_admin_approving_job_creates_activity_log(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'PT Natyaa Log', 'email_verified_at' => now()]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'AI Engineer',
            'department' => 'R&D',
            'type' => 'Full-time',
            'location' => 'Jakarta',
            'duration' => '1 Tahun',
            'status' => 'Tutup',
        ]);

        $this->actingAs($admin)->patch(route('admin.jobs.approve', $job));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'role' => 'admin',
            'action' => 'job_approved',
            'title' => 'Persetujuan Lowongan',
        ]);
    }
}
