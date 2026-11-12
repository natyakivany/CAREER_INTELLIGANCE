<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Occupation;
use App\Models\StudentProfile;
use App\Models\User;
use Database\Seeders\OccupationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OccupationSeeder::class);
    }

    public function test_student_dashboard_is_accessible_and_renders_workflow(): void
    {
        $user = User::factory()->create(['nim' => '2201092001']);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard Ringkasan Karier');
        $response->assertSee('Alur Penilaian');
        $response->assertSee('Employability Score');
    }

    public function test_student_can_update_academic_profile_with_target_field_and_occupation(): void
    {
        $user = User::factory()->create();
        $targetOccupation = Occupation::where('field', 'it')->first();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Natya Kivany',
                'email' => $user->email,
                'prodi' => 'D4 Teknologi Rekayasa Perangkat Lunak',
                'jurusan' => 'Teknologi Informasi',
                'semester' => 'Semester 7',
                'target_field' => 'it',
                'target_occupation_id' => $targetOccupation->id,
                'phone' => '081234567890',
                'ipk' => '3.85',
                'bio' => 'Mahasiswa tingkat akhir antusias di bidang full-stack development.',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->studentProfile);
        $this->assertSame('D4 Teknologi Rekayasa Perangkat Lunak', $user->studentProfile->prodi);
        $this->assertSame('it', $user->studentProfile->target_field);
        $this->assertSame($targetOccupation->id, $user->studentProfile->target_occupation_id);
    }

    public function test_student_can_add_and_delete_portfolio_project(): void
    {
        $user = User::factory()->create(['nim' => '2201092002']);

        // Add portfolio
        $response = $this
            ->actingAs($user)
            ->post('/portfolio', [
                'title' => 'Sistem Informasi Kampus Berbasis Laravel',
                'field' => 'it',
                'category' => 'Web App',
                'role' => 'Full Stack Developer',
                'technologies' => 'Laravel, MySQL, Tailwind CSS',
                'description' => 'Membangun aplikasi manajemen akademik terpadu untuk politeknik.',
                'project_url' => 'https://github.com/example/sim-kampus',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/portfolio');

        $this->assertDatabaseHas('portfolios', [
            'user_id' => $user->id,
            'title' => 'Sistem Informasi Kampus Berbasis Laravel',
            'field' => 'it',
        ]);

        $portfolio = $user->portfolios()->first();

        // Delete portfolio
        $deleteResponse = $this
            ->actingAs($user)
            ->delete('/portfolio/'.$portfolio->id);

        $deleteResponse->assertRedirect('/portfolio');
        $this->assertDatabaseMissing('portfolios', [
            'id' => $portfolio->id,
        ]);
    }

    public function test_student_can_start_and_submit_gemini_assessment(): void
    {
        $user = User::factory()->create(['nim' => '2201092003']);
        StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'D4 Teknik Komputer',
            'jurusan' => 'Teknologi Informasi',
            'semester' => 'Semester 7',
            'target_field' => 'it',
        ]);

        // Start assessment
        $startResponse = $this
            ->actingAs($user)
            ->post('/assessment/start', [
                'field' => 'it',
            ]);

        $assessment = Assessment::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($assessment);
        $this->assertSame('in_progress', $assessment->status);
        $this->assertGreaterThan(0, $assessment->questions()->count());

        $startResponse->assertRedirect(route('assessment.show', $assessment));

        // Submit assessment with answers
        $answers = [];
        foreach ($assessment->questions as $question) {
            $answers[$question->id] = 'B';
        }

        $submitResponse = $this
            ->actingAs($user)
            ->post(route('assessment.submit', $assessment), [
                'answers' => $answers,
            ]);

        $submitResponse->assertRedirect(route('assessment.result', $assessment));

        $assessment->refresh();
        $this->assertSame('completed', $assessment->status);
        $this->assertGreaterThanOrEqual(0, $assessment->total_score);
        $this->assertNotNull($assessment->ai_feedback);
    }

    public function test_student_can_view_skill_gap_analysis(): void
    {
        $user = User::factory()->create(['nim' => '2201092004']);
        $occupation = Occupation::where('field', 'it')->first();

        StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'D4 RPL',
            'jurusan' => 'Teknologi Informasi',
            'target_field' => 'it',
            'target_occupation_id' => $occupation->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/skill-gap');

        $response->assertOk();
        $response->assertSee('Skill Gap');
        $response->assertSee($occupation->title);
    }

    public function test_student_can_view_career_recommendations(): void
    {
        $user = User::factory()->create(['nim' => '2201092005']);
        StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'D4 Teknik Telekomunikasi',
            'jurusan' => 'Teknik Elektro',
            'target_field' => 'elektro',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/recommendation');

        $response->assertOk();
        $response->assertSee('Rekomendasi Karier');
        $response->assertSee('ELEKTRO');
    }

    public function test_student_can_view_and_toggle_roadmap_milestones(): void
    {
        $user = User::factory()->create(['nim' => '2201092006']);
        $occupation = Occupation::where('field', 'it')->first();

        StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'D4 RPL',
            'jurusan' => 'Teknologi Informasi',
            'target_field' => 'it',
            'target_occupation_id' => $occupation->id,
        ]);

        // First trigger gap computation to generate roadmaps
        $this->actingAs($user)->get('/skill-gap');

        $roadmapResponse = $this
            ->actingAs($user)
            ->get('/roadmap');

        $roadmapResponse->assertOk();
        $roadmapResponse->assertSee('Roadmap Karier');

        $roadmap = $user->careerRoadmaps()->first();
        $this->assertNotNull($roadmap);

        // Toggle task
        $toggleResponse = $this
            ->actingAs($user)
            ->post(route('roadmap.toggle', $roadmap), [
                'task_index' => 0,
            ]);

        $toggleResponse->assertRedirect('/roadmap');
        $roadmap->refresh();
        $this->assertSame('completed', $roadmap->action_items[0]['status']);
    }

    public function test_portfolio_completeness_percentage_is_displayed_and_calculated_correctly(): void
    {
        $user = User::factory()->create(['nim' => '2201092099']);

        // 1. Initially 0 projects -> 0% completeness
        $emptyResponse = $this
            ->actingAs($user)
            ->get('/portfolio');

        $emptyResponse->assertOk();
        $emptyResponse->assertSee('Kelengkapan Portofolio');
        $emptyResponse->assertSee('0%');

        // 2. Add 1 complete project with tech stack and live demo
        $user->portfolios()->create([
            'title' => 'Sistem Rekomendasi Karier AI',
            'field' => 'it',
            'category' => 'Web App',
            'description' => 'Aplikasi cerdas untuk menganalisis kesenjangan keterampilan mahasiswa.',
            'tech_stack' => 'Laravel, React, Tailwind CSS',
            'project_url' => 'https://example.com/demo',
        ]);
        $user->refresh();

        $filledResponse = $this
            ->actingAs($user)
            ->get('/portfolio');

        $filledResponse->assertOk();
        $filledResponse->assertSee('Kelengkapan Portofolio');
        // 1 project = 35%
        $filledResponse->assertSee('35%');
        $filledResponse->assertSee('Kelengkapan Data:');

        // Check profile page reflects portfolio completeness
        $profileResponse = $this
            ->actingAs($user)
            ->get('/profile');

        $profileResponse->assertOk();
        $profileResponse->assertSee('35% Lengkap');

        // Check dashboard reflects portfolio completeness
        $dashboardResponse = $this
            ->actingAs($user)
            ->get('/dashboard');

        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('35%');
    }
}
