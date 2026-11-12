<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Occupation;
use App\Models\StudentProfile;
use App\Models\User;
use Database\Seeders\OccupationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OccupationSeeder::class);
    }

    public function test_guest_is_redirected_to_login_from_admin_routes(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_and_mitra_cannot_access_admin_portal(): void
    {
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $mitra = User::factory()->create(['role' => 'mitra']);

        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($mitra)->get(route('admin.dashboard'))->assertForbidden();

        $this->actingAs($student)->get(route('admin.students'))->assertForbidden();
        $this->actingAs($mitra)->get(route('admin.students'))->assertForbidden();
        $this->actingAs($student)->get(route('mitra.dashboard'))->assertForbidden();
    }

    public function test_admin_has_super_admin_access_to_mitra_and_student_portals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Admin can access Mitra portal directly without 403
        $mitraResponse = $this->actingAs($admin)->get(route('mitra.dashboard'));
        $mitraResponse->assertOk();
        $mitraResponse->assertSee('Mode Super Admin');

        // Admin can access Student portal directly in preview mode without 403
        $studentResponse = $this->actingAs($admin)->get(route('dashboard', ['preview' => 1]));
        $studentResponse->assertOk();
        $studentResponse->assertSee('Mode Super Admin');
    }

    public function test_admin_can_view_admin_dashboard_with_metrics(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Santoso']);
        StudentProfile::create([
            'user_id' => $student->id,
            'prodi' => 'D4 Rekayasa Perangkat Lunak',
            'target_field' => 'it',
            'employability_score' => 85,
        ]);

        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'PT Mitra Solusi']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Pusat Kendali Administrator Kampus');
        $response->assertSee('Budi Santoso');
        $response->assertSee('PT Mitra Solusi');
        $response->assertSee('85 / 100');
    }

    public function test_admin_can_view_students_list_and_search(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $studentA = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Ahmad Dahlan', 'nim' => '2401081099']);
        $studentB = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Siti Nurhaliza', 'nim' => '2401081088']);

        $response = $this->actingAs($admin)->get(route('admin.students', ['search' => 'Ahmad']));

        $response->assertOk();
        $response->assertSee('Ahmad Dahlan');
        $response->assertSee('2401081099');
        $response->assertDontSee('Siti Nurhaliza');
    }

    public function test_admin_can_view_mitra_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mitra = User::factory()->create(['role' => 'mitra', 'name' => 'PT Inovindo Digital']);

        $response = $this->actingAs($admin)->get(route('admin.mitra'));

        $response->assertOk();
        $response->assertSee('Manajemen Mitra Industri');
        $response->assertSee('PT Inovindo Digital');
    }

    public function test_admin_can_view_occupations_benchmarks(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.occupations'));

        $response->assertOk();
        $response->assertSee('Standar Profesi');
        $response->assertSee('Benchmark Keahlian Inti');
    }

    public function test_admin_can_delete_a_user_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa', 'name' => 'User Hapus']);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $student));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $student->id]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_create_and_update_occupation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $postResponse = $this->actingAs($admin)->post(route('admin.occupations.store'), [
            'title' => 'Cyber Security Specialist',
            'field' => 'it',
            'category' => 'Network Security',
            'description' => 'Menjaga keamanan infrastruktur jaringan dan sistem.',
            'average_salary' => 'Rp 10.000.000 - Rp 20.000.000',
            'demand_level' => 'Sangat Tinggi',
            'skills' => 'Ethical Hacking, Penetration Testing, Linux',
        ]);

        $postResponse->assertRedirect();
        $this->assertDatabaseHas('occupations', ['title' => 'Cyber Security Specialist']);

        $occupation = Occupation::where('title', 'Cyber Security Specialist')->first();
        $this->assertCount(3, $occupation->skills);

        $putResponse = $this->actingAs($admin)->put(route('admin.occupations.update', $occupation), [
            'title' => 'Lead Cyber Security Engineer',
            'field' => 'it',
            'category' => 'Network Security',
            'description' => 'Deskripsi diperbarui.',
            'average_salary' => 'Rp 12.000.000 - Rp 25.000.000',
            'demand_level' => 'Sangat Tinggi',
        ]);

        $putResponse->assertRedirect();
        $this->assertDatabaseHas('occupations', ['title' => 'Lead Cyber Security Engineer']);
    }

    public function test_admin_can_add_and_delete_skill_for_occupation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $occupation = Occupation::first();

        $postResponse = $this->actingAs($admin)->post(route('admin.occupations.skills.store', $occupation), [
            'skill_name' => 'Go Programming',
            'benchmark_score' => 90,
            'is_core' => 1,
        ]);

        $postResponse->assertRedirect();
        $this->assertDatabaseHas('occupation_skills', [
            'occupation_id' => $occupation->id,
            'skill_name' => 'Go Programming',
        ]);

        $skill = $occupation->skills()->where('skill_name', 'Go Programming')->first();

        $delResponse = $this->actingAs($admin)->delete(route('admin.occupations.skills.destroy', $skill));
        $delResponse->assertRedirect();
        $this->assertDatabaseMissing('occupation_skills', ['id' => $skill->id]);
    }

    public function test_admin_can_create_student_manually(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.students.store'), [
            'name' => 'Mahasiswa Baru',
            'email' => 'mahasiswabaru@gmail.com',
            'nim' => '2401089999',
            'prodi' => 'D4 RPL',
            'target_field' => 'it',
            'semester' => 'Semester 5',
            'ipk' => 3.75,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'mahasiswabaru@gmail.com',
            'nim' => '2401089999',
            'role' => 'mahasiswa',
        ]);
        $this->assertDatabaseHas('student_profiles', [
            'prodi' => 'D4 RPL',
        ]);
    }

    public function test_admin_can_create_mitra_manually(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.mitra.store'), [
            'name' => 'PT Mitra Baru Sukses',
            'email' => 'mitrabaru@gmail.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'mitrabaru@gmail.com',
            'role' => 'mitra',
        ]);
    }

    public function test_admin_can_reset_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['password' => bcrypt('old-secret')]);

        $response = $this->actingAs($admin)->post(route('admin.users.reset-password', $student));

        $response->assertRedirect();
        $student->refresh();
        $this->assertTrue(Hash::check('', $student->password) || Hash::check('password', $student->password));
    }

    public function test_admin_can_view_and_delete_assessment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $assessment = Assessment::create([
            'user_id' => $student->id,
            'field' => 'it',
            'status' => 'completed',
            'total_score' => 88,
        ]);

        $viewResponse = $this->actingAs($admin)->get(route('admin.assessments'));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Pengawasan Asesmen Gemini AI');

        $delResponse = $this->actingAs($admin)->delete(route('admin.assessments.destroy', $assessment));
        $delResponse->assertRedirect();
        $this->assertDatabaseMissing('assessments', ['id' => $assessment->id]);
    }

    public function test_admin_can_view_print_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.reports.print'));

        $response->assertOk();
        $response->assertSee('Laporan Rekapitulasi Evaluasi Kesiapan Kerja Mahasiswa');
    }

    public function test_admin_can_view_leaderboards_and_statistics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.leaderboards'));

        $response->assertOk();
        $response->assertSee('Peringkat & Statistik Ekosistem', false);
        $response->assertSee('Peringkat Mitra Industri');
        $response->assertSee('Peringkat Mahasiswa Top Skor');
    }
}
