<?php

namespace Tests\Feature;

use App\Models\StudentProfile;
use App\Models\User;
use Database\Seeders\OccupationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingLevelSeparationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OccupationSeeder::class);
    }

    public function test_student_profiles_correctly_separate_mahasiswa_and_smk(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Mahasiswa Perguruan Tinggi
        $mahasiswaUser = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Mahasiswa']);
        $mahasiswaProfile = StudentProfile::create([
            'user_id' => $mahasiswaUser->id,
            'prodi' => 'Teknik Komputer',
            'jurusan' => 'Teknologi Informasi (Perguruan Tinggi)',
            'semester' => 'Semester 5',
            'institution_name' => 'Politeknik Negeri Padang',
            'employability_score' => 90,
        ]);

        // 2. Siswa SMK
        $smkUser = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Siti Siswa SMK']);
        $smkProfile = StudentProfile::create([
            'user_id' => $smkUser->id,
            'prodi' => 'Teknik Komputer & Jaringan (TKJ)',
            'jurusan' => 'SMK - Teknologi Informasi & Komunikasi',
            'semester' => 'Siswa SMK/SMA Kelas 12',
            'institution_name' => 'SMKN 1 Padang',
            'employability_score' => 80,
        ]);

        $this->assertEquals('Mahasiswa', $mahasiswaProfile->tingkat);
        $this->assertTrue($mahasiswaProfile->isMahasiswa());
        $this->assertFalse($mahasiswaProfile->isSmk());

        $this->assertEquals('SMK', $smkProfile->tingkat);
        $this->assertTrue($smkProfile->isSmk());
        $this->assertFalse($smkProfile->isMahasiswa());

        $this->assertEquals(1, StudentProfile::mahasiswa()->count());
        $this->assertEquals(1, StudentProfile::smk()->count());

        // Test leaderboard page separates them
        $response = $this->actingAs($admin)->get(route('admin.leaderboards'));
        $response->assertOk();
        $response->assertSee('Tingkat Mahasiswa (PT)');
        $response->assertSee('Tingkat SMK / Vokasi');
        $response->assertSee('Budi Mahasiswa');
        $response->assertSee('Siti Siswa SMK');

        // Test student management filter by tingkat
        $filterMhs = $this->actingAs($admin)->get(route('admin.students', ['tingkat' => 'mahasiswa']));
        $filterMhs->assertOk();
        $filterMhs->assertSee('Budi Mahasiswa');
        $filterMhs->assertDontSee('Siti Siswa SMK');

        $filterSmk = $this->actingAs($admin)->get(route('admin.students', ['tingkat' => 'smk']));
        $filterSmk->assertOk();
        $filterSmk->assertSee('Siti Siswa SMK');
        $filterSmk->assertDontSee('Budi Mahasiswa');

        // Test print report by tingkat
        $printSmk = $this->actingAs($admin)->get(route('admin.reports.print', ['tingkat' => 'SMK']));
        $printSmk->assertOk();
        $printSmk->assertSee('Siti Siswa SMK');
        $printSmk->assertDontSee('Budi Mahasiswa');
    }
}
