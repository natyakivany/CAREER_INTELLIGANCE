<?php

namespace Tests\Feature;

use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\NewJobApplicationNotification;
use Database\Seeders\OccupationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationIndicatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OccupationSeeder::class);
    }

    public function test_student_sees_notification_indicator_when_unread_notifications_exist(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'email_verified_at' => now()]);
        $mitra = User::factory()->create(['role' => 'mitra', 'email_verified_at' => now()]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Software Engineer',
            'department' => 'Teknologi Informasi',
            'type' => 'Pekerjaan Mandiri Kampus',
            'location' => 'Padang',
            'duration' => '6 Bulan',
            'skills' => ['PHP', 'Laravel'],
            'status' => 'Buka',
        ]);

        $application = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Diterima',
            'match_score' => 85,
        ]);

        // Send notification to student
        $student->notify(new ApplicationStatusUpdated($application));

        $this->assertEquals(1, $student->unreadNotifications()->count());

        $response = $this->actingAs($student)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Notifikasi Lamaran');
        $response->assertSee('1 Baru');
    }

    public function test_mitra_sees_notification_indicator_when_new_applicant_applies(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'email_verified_at' => now()]);
        $mitra = User::factory()->create(['role' => 'mitra', 'email_verified_at' => now()]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'UI/UX Designer',
            'department' => 'Teknologi Informasi',
            'type' => 'Pekerjaan Mandiri Kampus',
            'location' => 'Padang',
            'duration' => '6 Bulan',
            'skills' => ['Figma'],
            'status' => 'Buka',
        ]);

        $application = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Menunggu Review',
            'match_score' => 90,
        ]);

        // Send notification to mitra
        $mitra->notify(new NewJobApplicationNotification($application));

        $this->assertEquals(1, $mitra->unreadNotifications()->count());

        $response = $this->actingAs($mitra)->get(route('mitra.dashboard'));
        $response->assertOk();
        $response->assertSee('Notifikasi Mitra');
        $response->assertSee('1 Baru');
    }

    public function test_admin_sees_notification_indicator_for_pending_reset_or_approvals(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'email_verified_at' => now()]);

        PasswordResetRequest::create([
            'user_id' => $student->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Notifikasi Administrator');
        $response->assertSee('1 Baru');
    }

    public function test_notification_go_route_marks_as_read_and_redirects(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'email_verified_at' => now()]);
        $mitra = User::factory()->create(['role' => 'mitra', 'email_verified_at' => now()]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Backend Developer',
            'department' => 'Teknologi Informasi',
            'type' => 'Pekerjaan Mandiri Kampus',
            'location' => 'Padang',
            'duration' => '6 Bulan',
            'skills' => ['PHP'],
            'status' => 'Buka',
        ]);

        $application = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Diterima',
            'match_score' => 95,
        ]);

        $student->notify(new ApplicationStatusUpdated($application));
        $notification = $student->unreadNotifications()->first();

        $this->assertNotNull($notification);

        $response = $this->actingAs($student)->get(route('notifications.go', $notification->id));
        $response->assertRedirect(route('applications.index'));

        $this->assertEquals(0, $student->fresh()->unreadNotifications()->count());
    }

    public function test_notifications_read_all(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa', 'email_verified_at' => now()]);
        $mitra = User::factory()->create(['role' => 'mitra', 'email_verified_at' => now()]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Web Developer',
            'department' => 'Teknologi Informasi',
            'type' => 'Pekerjaan Mandiri Kampus',
            'location' => 'Padang',
            'duration' => '6 Bulan',
            'skills' => ['PHP'],
            'status' => 'Buka',
        ]);

        $application = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Diterima',
            'match_score' => 80,
        ]);

        $student->notify(new ApplicationStatusUpdated($application));
        $this->assertEquals(1, $student->unreadNotifications()->count());

        $response = $this->actingAs($student)->post(route('notifications.readAll'));
        $response->assertRedirect();

        $this->assertEquals(0, $student->fresh()->unreadNotifications()->count());
    }
}
