<?php

namespace Tests\Feature;

use App\Models\JobListing;
use App\Models\Occupation;
use App\Models\Portfolio;
use App\Models\StudentProfile;
use App\Models\User;
use Database\Seeders\OccupationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OccupationSeeder::class);
    }

    public function test_mitra_dashboard_displays_candidate_recommendations_based_on_open_jobs(): void
    {
        $mitra = User::factory()->create([
            'role' => 'mitra',
            'name' => 'PT Teknologi Unggul',
            'email_verified_at' => now(),
        ]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Web Application Developer',
            'department' => 'Software Engineering',
            'type' => 'Full-time',
            'location' => 'Jakarta',
            'duration' => '6 Bulan',
            'skills' => ['Laravel', 'MySQL', 'Vue.js'],
            'status' => 'Buka',
        ]);

        $occupation = Occupation::first();

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Rizky Pratama',
            'email_verified_at' => now(),
        ]);

        StudentProfile::create([
            'user_id' => $student->id,
            'prodi' => 'Teknik Rekayasa Perangkat Lunak',
            'jurusan' => 'Jurusan TI',
            'target_occupation_id' => $occupation->id,
            'employability_score' => 85,
        ]);

        Portfolio::create([
            'user_id' => $student->id,
            'title' => 'Sistem Rekomendasi Karier',
            'tech_stack' => 'Laravel, MySQL, Tailwind CSS',
            'project_url' => 'https://example.com',
        ]);

        $response = $this->actingAs($mitra)->get(route('mitra.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kandidat Rekomendasi Sesuai Lowongan');
        $response->assertSee('Rizky Pratama');
        $response->assertSee('Web Application Developer');
    }

    public function test_mitra_can_filter_candidates_by_specific_open_job(): void
    {
        $mitra = User::factory()->create([
            'role' => 'mitra',
            'name' => 'PT Inovasi Digital',
            'email_verified_at' => now(),
        ]);

        $jobWeb = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Full Stack Developer',
            'department' => 'Software Engineering',
            'type' => 'Full-time',
            'location' => 'Bandung',
            'duration' => '6 Bulan',
            'skills' => ['Laravel', 'Vue.js'],
            'status' => 'Buka',
        ]);

        $occupation = Occupation::first();

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Anisa Rahma',
            'email_verified_at' => now(),
        ]);

        StudentProfile::create([
            'user_id' => $student->id,
            'prodi' => 'Teknik Komputer',
            'jurusan' => 'Jurusan TI',
            'target_occupation_id' => $occupation->id,
            'employability_score' => 80,
        ]);

        Portfolio::create([
            'user_id' => $student->id,
            'title' => 'E-Commerce Platform',
            'tech_stack' => 'Laravel, Vue.js',
        ]);

        $response = $this->actingAs($mitra)->get(route('mitra.candidates', ['job_id' => $jobWeb->id]));

        $response->assertStatus(200);
        $response->assertSee('Full Stack Developer');
        $response->assertSee('Anisa Rahma');
        $response->assertSee('Skill Cocok');
    }

    public function test_mitra_candidate_detail_shows_open_jobs_compatibility(): void
    {
        $mitra = User::factory()->create([
            'role' => 'mitra',
            'name' => 'PT Solusi Cerdas',
            'email_verified_at' => now(),
        ]);

        $job = JobListing::create([
            'user_id' => $mitra->id,
            'title' => 'Backend API Engineer',
            'department' => 'Software Engineering',
            'type' => 'Full-time',
            'location' => 'Surabaya',
            'duration' => '12 Bulan',
            'skills' => ['PHP', 'RESTful API & Backend'],
            'status' => 'Buka',
        ]);

        $occupation = Occupation::first();

        $student = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Dimas Wicaksono',
            'email_verified_at' => now(),
        ]);

        StudentProfile::create([
            'user_id' => $student->id,
            'prodi' => 'Teknik Komputer',
            'jurusan' => 'Jurusan TI',
            'target_occupation_id' => $occupation->id,
            'employability_score' => 75,
        ]);

        Portfolio::create([
            'user_id' => $student->id,
            'title' => 'RESTful API Service',
            'tech_stack' => 'PHP, RESTful API & Backend',
        ]);

        $response = $this->actingAs($mitra)->get(route('mitra.candidates.show', $student->id));

        $response->assertStatus(200);
        $response->assertSee('Kesesuaian dengan Lowongan Buka Anda');
        $response->assertSee('Backend API Engineer');
    }
}
