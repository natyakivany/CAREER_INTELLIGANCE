<?php

namespace Tests\Feature;

use App\Mail\JobApplicationStatusMail;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\CandidateResponseNotification;
use Database\Seeders\OccupationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InterviewSchedulingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OccupationSeeder::class);
    }

    public function test_mitra_can_schedule_interview_and_student_receives_date_in_portal(): void
    {
        Mail::fake();
        Notification::fake();

        $mitra = User::factory()->create([
            'role' => 'mitra',
            'name' => 'PT Teknologi Mitra',
            'email_verified_at' => now(),
        ]);

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Budi Santoso',
            'email_verified_at' => now(),
        ]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Backend Developer Intern',
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
            'status' => 'Menunggu Review',
            'match_score' => 88,
        ]);

        $interviewTime = now()->addDays(3)->setTime(14, 0, 0);
        $meetingLink = 'https://meet.google.com/xyz-uvwx-rst';

        // Mitra sets interview schedule
        $response = $this->actingAs($mitra)->patch(route('mitra.applications.status', $application), [
            'status' => 'Wawancara',
            'interview_date' => $interviewTime->format('Y-m-d\TH:i'),
            'interview_link' => $meetingLink,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check DB update
        $application->refresh();
        $this->assertEquals('Wawancara', $application->status);
        $this->assertNotNull($application->interview_date);
        $this->assertEquals($interviewTime->format('Y-m-d H:i'), $application->interview_date->format('Y-m-d H:i'));
        $this->assertEquals($meetingLink, $application->interview_link);
        $this->assertEquals('Menunggu Konfirmasi', $application->interview_status);

        // Check mail & notification sent
        Mail::assertSent(JobApplicationStatusMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email);
        });

        Notification::assertSentTo($student, ApplicationStatusUpdated::class, function ($notification) use ($application, $meetingLink) {
            $data = $notification->toArray($application->user);

            return $data['status'] === 'Wawancara'
                && ! empty($data['interview_date'])
                && $data['interview_link'] === $meetingLink
                && str_contains($data['message'], 'Undangan Wawancara');
        });

        // Student checks their dashboard: upcoming interview banner must be visible
        $dashboardResponse = $this->actingAs($student)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Jadwal Wawancara Anda');
        $dashboardResponse->assertSee('Backend Developer Intern');
        $dashboardResponse->assertSee('PT Teknologi Mitra');
        $dashboardResponse->assertSee($interviewTime->translatedFormat('l, d F Y - H:i'));
        $dashboardResponse->assertSee($meetingLink);
        $dashboardResponse->assertSee('Konfirmasi Hadir');

        // Student checks their applications history
        $appsResponse = $this->actingAs($student)->get(route('applications.index'));
        $appsResponse->assertOk();
        $appsResponse->assertSee('Jadwal Wawancara Resmi');
        $appsResponse->assertSee($interviewTime->translatedFormat('l, d F Y'));
        $appsResponse->assertSee($interviewTime->format('H:i'));
        $appsResponse->assertSee($meetingLink);
        $appsResponse->assertSee('Konfirmasi Hadir');

        // Student confirms attendance
        $confirmResponse = $this->actingAs($student)->patch(route('applications.confirm', $application), [
            'interview_status' => 'Hadir',
        ]);
        $confirmResponse->assertRedirect();
        $confirmResponse->assertSessionHas('success');

        $this->assertEquals('Hadir', $application->fresh()->interview_status);

        Notification::assertSentTo($mitra, CandidateResponseNotification::class, function ($n) {
            return $n->responseType === 'Bersedia';
        });

        // Dashboard now reflects confirmed attendance
        $dashboardResponse2 = $this->actingAs($student)->get(route('dashboard'));
        $dashboardResponse2->assertOk();
        $dashboardResponse2->assertSee('Kehadiran Dikonfirmasi Hadir');
    }

    public function test_mitra_can_reschedule_interview_and_student_can_decline(): void
    {
        Mail::fake();
        Notification::fake();

        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'PT Mitra Baru', 'email_verified_at' => now()]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Siti Nurhaliza', 'email_verified_at' => now()]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Frontend Engineer',
            'department' => 'Teknologi Informasi',
            'type' => 'Pekerjaan Mandiri Kampus',
            'location' => 'Padang',
            'duration' => '6 Bulan',
            'skills' => ['React', 'Tailwind'],
            'status' => 'Buka',
        ]);

        $firstDate = now()->addDays(2)->setTime(10, 0, 0);
        $application = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Wawancara',
            'interview_date' => $firstDate,
            'interview_link' => 'https://meet.google.com/old-link',
            'interview_status' => 'Hadir',
            'match_score' => 90,
        ]);

        // Mitra reschedules interview
        $rescheduledDate = now()->addDays(5)->setTime(15, 30, 0);
        $newLink = 'https://zoom.us/j/987654321';

        $response = $this->actingAs($mitra)->patch(route('mitra.applications.status', $application), [
            'status' => 'Wawancara',
            'interview_date' => $rescheduledDate->format('Y-m-d\TH:i'),
            'interview_link' => $newLink,
        ]);

        $response->assertRedirect();
        $application->refresh();

        $this->assertEquals($rescheduledDate->format('Y-m-d H:i'), $application->interview_date->format('Y-m-d H:i'));
        $this->assertEquals($newLink, $application->interview_link);
        $this->assertEquals('Menunggu Konfirmasi', $application->interview_status);

        // Student declines interview
        $declineResponse = $this->actingAs($student)->patch(route('applications.confirm', $application), [
            'interview_status' => 'Tidak Hadir',
        ]);
        $declineResponse->assertRedirect();

        $this->assertEquals('Tidak Hadir', $application->fresh()->interview_status);

        Notification::assertSentTo($mitra, CandidateResponseNotification::class, function ($n) {
            return $n->responseType === 'Tidak Bersedia';
        });

        // Student dashboard shows declined status
        $dashboardResponse = $this->actingAs($student)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Jadwal Ditolak / Batal');
    }

    public function test_mitra_application_stage_specific_buttons_and_tabs(): void
    {
        Notification::fake();
        Mail::fake();

        $mitra = User::factory()->create([
            'role' => 'mitra',
            'name' => 'PT Solusi Terbuka',
            'email_verified_at' => now(),
        ]);

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Rian Pratama',
            'email_verified_at' => now(),
        ]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Mobile App Developer',
            'department' => 'Engineering',
            'type' => 'Pekerjaan Mandiri Kampus',
            'location' => 'Jakarta',
            'duration' => '3 Bulan',
            'skills' => ['Flutter'],
            'status' => 'Buka',
        ]);

        $app = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Menunggu Review',
            'match_score' => 85,
        ]);

        // 1. In 'Menunggu Review': Mitra sees 'Lanjut ke Tes' and does NOT see 'Terima Kerja'
        $viewResponse = $this->actingAs($mitra)->get(route('mitra.applications.show', $job));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Menunggu Review');
        $viewResponse->assertSee('Lanjut ke Tes');
        $viewResponse->assertDontSee('Terima Kerja');

        // Mitra advances application to 'Proses Seleksi' (Tes)
        $this->actingAs($mitra)->patch(route('mitra.applications.status', $app), [
            'status' => 'Proses Seleksi',
        ]);
        $this->assertEquals('Proses Seleksi', $app->fresh()->status);

        // 2. In 'Proses Seleksi': Mitra sees 'Menunggu Mahasiswa'
        $viewResponse2 = $this->actingAs($mitra)->get(route('mitra.applications.show', $job));
        $viewResponse2->assertOk();
        $viewResponse2->assertSee('Menunggu Mahasiswa');

        // Mitra advances application to 'Wawancara'
        $this->actingAs($mitra)->patch(route('mitra.applications.status', $app), [
            'status' => 'Wawancara',
            'interview_date' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ]);
        $this->assertEquals('Wawancara', $app->fresh()->status);

        // 3. In 'Wawancara': Mitra sees 'Wawancara (Menunggu Mahasiswa)'
        $viewResponse3 = $this->actingAs($mitra)->get(route('mitra.applications.show', $job));
        $viewResponse3->assertOk();
        $viewResponse3->assertSee('Wawancara (Menunggu Mahasiswa)');
    }

    public function test_student_confirming_test_selection_stage_notifies_mitra(): void
    {
        Notification::fake();
        Mail::fake();

        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'PT Mitra Sejati', 'email_verified_at' => now()]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Natya Kivany', 'email_verified_at' => now()]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Full Stack Developer',
            'department' => 'Teknologi Informasi',
            'type' => 'Pekerjaan Mandiri Kampus',
            'location' => 'Padang',
            'duration' => '6 Bulan',
            'skills' => ['Laravel', 'Vue'],
            'status' => 'Buka',
        ]);

        $application = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Proses Seleksi',
            'interview_status' => 'Menunggu Konfirmasi',
            'match_score' => 95,
        ]);

        // Student views their applications page: sees invitation to test
        $appsPage = $this->actingAs($student)->get(route('applications.index'));
        $appsPage->assertOk();
        $appsPage->assertSee('Undangan Tes / Seleksi Khusus');
        $appsPage->assertSee('Bersedia Mengikuti');
        $appsPage->assertSee('Tidak Bersedia');

        // Student confirms they are willing to take the test
        $response = $this->actingAs($student)->patch(route('applications.confirm', $application), [
            'interview_status' => 'Hadir',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Anda telah mengonfirmasi bersedia mengikuti tes seleksi.');

        $this->assertEquals('Hadir', $application->fresh()->interview_status);

        // Mitra receives CandidateResponseNotification
        Notification::assertSentTo($mitra, CandidateResponseNotification::class, function ($n) use ($job, $student) {
            $data = $n->toArray($student);

            return $n->responseType === 'Bersedia'
                && $data['type'] === 'candidate_response'
                && $data['job_id'] === $job->id
                && str_contains($data['message'], 'bersedia mengikuti Tes / Seleksi');
        });

        // Mitra portal shows confirmed state
        $mitraView = $this->actingAs($mitra)->get(route('mitra.applications.show', $job));
        $mitraView->assertOk();
        $mitraView->assertSee('Mahasiswa Bersedia (Tes)');
    }

    public function test_student_receives_in_app_notification_even_if_smtp_fails(): void
    {
        Notification::fake();
        Mail::shouldReceive('to->send')->andThrow(new \Exception('SMTP connection error'));

        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'PT Cloud Inovasi', 'email_verified_at' => now()]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Natya Kivany', 'email_verified_at' => now()]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Software Engineer',
            'department' => 'Engineering',
            'type' => 'Pekerjaan Mandiri Kampus',
            'location' => 'Jakarta',
            'duration' => '6 Bulan',
            'skills' => ['Laravel'],
            'status' => 'Buka',
        ]);

        $application = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Menunggu Review',
            'match_score' => 90,
        ]);

        // Mitra changes status to 'Proses Seleksi'
        $response = $this->actingAs($mitra)->patch(route('mitra.applications.status', $application), [
            'status' => 'Proses Seleksi',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Proses Seleksi', $application->fresh()->status);

        // Student MUST receive the in-app notification despite SMTP failure
        Notification::assertSentTo($student, ApplicationStatusUpdated::class, function ($n) use ($application) {
            $data = $n->toArray($application->user);

            return $data['status'] === 'Proses Seleksi';
        });
    }

    public function test_mitra_can_schedule_offline_interview_with_location(): void
    {
        Mail::fake();
        Notification::fake();

        $mitra = User::factory()->create([
            'role' => 'mitra',
            'name' => 'PT Solusi Digital',
            'email_verified_at' => now(),
        ]);

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Ahmad Fauzi',
            'email_verified_at' => now(),
        ]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Flutter Developer',
            'department' => 'Mobile Development',
            'type' => 'Pekerjaan Mandiri Kampus',
            'location' => 'Padang',
            'duration' => '6 Bulan',
            'skills' => ['Flutter', 'Dart'],
            'status' => 'Buka',
        ]);

        $application = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Menunggu Review',
            'match_score' => 92,
        ]);

        $interviewTime = now()->addDays(4)->setTime(9, 30, 0);
        $offlineLocation = 'Gedung Menara Mandiri Lt. 5, Ruang Diskusi 2, Jl. Sudirman No. 10';

        // Mitra schedules offline interview
        $response = $this->actingAs($mitra)->patch(route('mitra.applications.status', $application), [
            'status' => 'Wawancara',
            'interview_type' => 'offline',
            'interview_location' => $offlineLocation,
            'interview_date' => $interviewTime->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check DB update
        $application->refresh();
        $this->assertEquals('Wawancara', $application->status);
        $this->assertEquals('offline', $application->interview_type);
        $this->assertEquals($offlineLocation, $application->interview_location);
        $this->assertTrue($application->isOfflineInterview());
        $this->assertFalse($application->isOnlineInterview());
        $this->assertEquals('Offline (Tatap Muka)', $application->interview_type_label);

        // Check Mail & Notification
        Mail::assertSent(JobApplicationStatusMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email);
        });

        Notification::assertSentTo($student, ApplicationStatusUpdated::class, function ($notification) use ($application, $offlineLocation) {
            $data = $notification->toArray($application->user);

            return $data['status'] === 'Wawancara'
                && ($data['interview_type'] ?? null) === 'offline'
                && ($data['interview_location'] ?? null) === $offlineLocation
                && str_contains($data['message'], 'Offline (Tatap Muka)');
        });

        // Student checks dashboard: offline badge and location visible
        $dashboardResponse = $this->actingAs($student)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Offline');
        $dashboardResponse->assertSee($offlineLocation);
        $dashboardResponse->assertSee('Lokasi:');

        // Student checks applications history: offline badge and location visible
        $appsResponse = $this->actingAs($student)->get(route('applications.index'));
        $appsResponse->assertOk();
        $appsResponse->assertSee('Wawancara Offline (Tatap Muka)');
        $appsResponse->assertSee($offlineLocation);
        $appsResponse->assertSee('Lokasi Pertemuan (Offline)');

        // Student confirms attendance
        $confirmResponse = $this->actingAs($student)->patch(route('applications.confirm', $application), [
            'interview_status' => 'Hadir',
        ]);
        $confirmResponse->assertRedirect();
        $this->assertEquals('Hadir', $application->fresh()->interview_status);
    }

    public function test_interview_mode_automatically_offline_when_link_empty_and_online_when_link_filled(): void
    {
        Mail::fake();
        Notification::fake();

        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'PT Mitra Otomatis', 'email_verified_at' => now()]);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Otomatis', 'email_verified_at' => now()]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'DevOps Engineer',
            'department' => 'Operations',
            'type' => 'Pekerjaan Mandiri Kampus',
            'location' => 'Jakarta Selatan',
            'duration' => '6 Bulan',
            'skills' => ['Docker', 'Linux'],
            'status' => 'Buka',
        ]);

        $app = JobApplication::create([
            'user_id' => $student->id,
            'job_listing_id' => $job->id,
            'status' => 'Menunggu Review',
            'match_score' => 88,
        ]);

        // 1. Mitra schedules interview WITHOUT link -> Must automatically be OFFLINE
        $this->actingAs($mitra)->patch(route('mitra.applications.status', $app), [
            'status' => 'Wawancara',
            'interview_date' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'interview_link' => '', // Empty link
        ]);

        $app->refresh();
        $this->assertEquals('offline', $app->interview_type);
        $this->assertTrue($app->isOfflineInterview());
        $this->assertFalse($app->isOnlineInterview());
        $this->assertEquals('Offline (Tatap Muka)', $app->interview_type_label);

        // Student portal shows offline badges & automatic office location
        $portalResponse = $this->actingAs($student)->get(route('applications.index'));
        $portalResponse->assertOk();
        $portalResponse->assertSee('Wawancara Offline (Tatap Muka)');
        $portalResponse->assertSee('Kantor PT Mitra Otomatis');

        // 2. Mitra updates interview WITH link -> Must automatically be ONLINE
        $this->actingAs($mitra)->patch(route('mitra.applications.status', $app), [
            'status' => 'Wawancara',
            'interview_date' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'interview_link' => 'https://meet.google.com/abc-defg-hij',
        ]);

        $app->refresh();
        $this->assertEquals('online', $app->interview_type);
        $this->assertTrue($app->isOnlineInterview());
        $this->assertFalse($app->isOfflineInterview());
        $this->assertEquals('Online (Daring)', $app->interview_type_label);

        // Student portal shows online badges & meeting link
        $portalResponse2 = $this->actingAs($student)->get(route('applications.index'));
        $portalResponse2->assertOk();
        $portalResponse2->assertSee('Wawancara Online (Daring)');
        $portalResponse2->assertSee('https://meet.google.com/abc-defg-hij');

        // 3. Mitra switches the existing online interview to Offline
        $this->actingAs($mitra)->patch(route('mitra.applications.status', $app), [
            'status' => 'Wawancara',
            'interview_type' => 'offline',
            'interview_date' => now()->addDays(4)->format('Y-m-d\TH:i'),
            'interview_location' => 'Kantor PT Mitra, Ruang Rapat Lt. 2',
        ]);

        $app->refresh();
        $this->assertEquals('offline', $app->interview_type);
        $this->assertNull($app->interview_link); // Must be cleared to null
        $this->assertEquals('Kantor PT Mitra, Ruang Rapat Lt. 2', $app->interview_location);
        $this->assertTrue($app->isOfflineInterview());
        $this->assertFalse($app->isOnlineInterview());
    }
}
