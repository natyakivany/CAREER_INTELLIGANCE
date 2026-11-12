<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\StudentProfile;
use App\Models\User;
use Database\Seeders\OccupationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CvBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OccupationSeeder::class);
    }

    public function test_student_can_view_cv_builder_with_dynamic_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Natya Kivany',
            'nim' => '2201092001',
        ]);

        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'D4 Rekayasa Perangkat Lunak',
            'jurusan' => 'Teknologi Informasi',
            'semester' => 'Semester 7',
            'target_field' => 'it',
            'phone' => '08123456789',
        ]);

        $portfolio = Portfolio::create([
            'user_id' => $user->id,
            'title' => 'Aplikasi Monitoring Magang',
            'field' => 'it',
            'category' => 'Web Application',
            'tech_stack' => 'Laravel, Vue.js',
            'description' => 'Membangun sistem pemantauan magang mahasiswa politeknik.',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/cv-builder');

        $response->assertOk();
        $response->assertSee('CV Builder ATS-Friendly');
        $response->assertSee('Natya Kivany');
        $response->assertSee('Aplikasi Monitoring Magang');
        $response->assertSee('Politeknik Negeri Padang');
    }

    public function test_student_can_update_cv_fields(): void
    {
        $user = User::factory()->create(['name' => 'Original Name']);
        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'target_field' => 'it',
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/cv-builder', [
                'name' => 'Updated Name',
                'phone' => '08987654321',
                'cv_headline' => 'Lead Full Stack Engineer',
                'cv_summary' => 'Ringkasan profesional yang telah disesuaikan secara manual.',
                'cv_custom_skills' => 'Laravel, React, Docker, Kubernetes',
            ]);

        $response->assertRedirect('/cv-builder');
        $response->assertSessionHas('status');

        $user->refresh();
        $profile->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('08987654321', $profile->phone);
        $this->assertSame('Lead Full Stack Engineer', $profile->cv_headline);
        $this->assertSame('Ringkasan profesional yang telah disesuaikan secara manual.', $profile->cv_summary);
        $this->assertContains('Laravel', $profile->cv_custom_skills);
    }

    public function test_student_can_generate_ai_cv_content(): void
    {
        $user = User::factory()->create();
        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'D4 RPL',
            'jurusan' => 'TI',
            'target_field' => 'it',
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/cv-builder/ai-generate');

        $response->assertRedirect('/cv-builder');
        $response->assertSessionHas('status');

        $profile->refresh();
        $this->assertNotEmpty($profile->cv_headline);
        $this->assertNotEmpty($profile->cv_summary);
    }

    public function test_cv_is_inactive_when_only_student_data_is_filled(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'role' => 'mahasiswa',
        ]);

        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'Teknik Komputer',
            'jurusan' => 'Teknologi Informasi',
            'semester' => 'Semester 7',
            'target_field' => 'it',
        ]);

        $this->assertFalse($profile->isCvFilled());
        $this->assertFalse($user->isCvFilled());

        // Attempting to print while CV is inactive should redirect with error
        $response = $this
            ->actingAs($user)
            ->get('/cv-builder/print');

        $response->assertRedirect('/cv-builder');
        $response->assertSessionHas('error');
    }

    public function test_student_can_access_cv_print_view_when_cv_is_filled(): void
    {
        $user = User::factory()->create(['name' => 'Natya Kivany']);
        $profile = StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'D4 RPL',
            'jurusan' => 'Teknologi Informasi',
            'target_field' => 'it',
            'cv_headline' => 'Junior Web Developer',
            'cv_summary' => 'Mahasiswa vokasi berpengalaman membangun aplikasi Laravel dan Vue.js.',
        ]);

        $this->assertTrue($profile->isCvFilled());
        $this->assertTrue($user->isCvFilled());

        $response = $this
            ->actingAs($user)
            ->get('/cv-builder/print');

        $response->assertOk();
        $response->assertSee('Natya Kivany');
        $response->assertSee('Cetak / Simpan PDF');
        $response->assertSee('Politeknik Negeri Padang');
    }

    public function test_admin_cannot_view_student_cv_until_student_fills_cv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create([
            'name' => 'Mahasiswa Baru',
            'role' => 'mahasiswa',
        ]);

        $profile = StudentProfile::create([
            'user_id' => $student->id,
            'prodi' => 'Teknik Komputer',
            'target_field' => 'it',
        ]);

        // When only student data is filled, admin trying to access CV gets redirected
        $response = $this
            ->actingAs($admin)
            ->get(route('admin.students.cv', $student->id));

        $response->assertRedirect(route('admin.students.show', $student->id));
        $response->assertSessionHas('error');

        // Once student fills CV, admin can access it
        $profile->update([
            'cv_headline' => 'Network Engineer',
            'cv_summary' => 'Spesialis jaringan komputer dan routing terkonfigurasi.',
        ]);

        $responseActive = $this
            ->actingAs($admin)
            ->get(route('admin.students.cv', $student->id));

        $responseActive->assertOk();
        $responseActive->assertSee('Mahasiswa Baru');
    }
}
