<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\StudentProfile;
use App\Models\User;
use Database\Seeders\OccupationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillAssessmentVarietyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OccupationSeeder::class);
    }

    public function test_assessment_generates_ten_questions_with_hard_and_soft_skills(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'D4 Rekayasa Perangkat Lunak',
            'target_field' => 'it',
        ]);

        $response = $this->actingAs($user)->post(route('assessment.start'), ['field' => 'it']);
        $response->assertRedirect();

        $assessment = Assessment::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($assessment);
        $this->assertSame(10, $assessment->questions()->count());
        $this->assertSame(10, $assessment->total_questions);

        $hardCount = $assessment->questions()->where('type', 'hard_skill')->count();
        $softCount = $assessment->questions()->where('type', 'soft_skill')->count();

        $this->assertSame(7, $hardCount);
        $this->assertSame(3, $softCount);
    }

    public function test_subsequent_assessment_provides_different_questions(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'D4 Rekayasa Perangkat Lunak',
            'target_field' => 'it',
        ]);

        // Sesi Asesmen Pertama
        $this->actingAs($user)->post(route('assessment.start'), ['field' => 'it']);
        $assessment1 = Assessment::where('user_id', $user->id)->latest()->first();
        $questions1 = $assessment1->questions()->pluck('question_text')->toArray();

        // Selesaikan Sesi 1
        $answers = [];
        foreach ($assessment1->questions as $q) {
            $answers[$q->id] = 'A';
        }
        $this->actingAs($user)->post(route('assessment.submit', $assessment1), ['answers' => $answers]);

        // Sesi Asesmen Kedua
        $this->actingAs($user)->post(route('assessment.start'), ['field' => 'it']);
        $assessment2 = Assessment::where('user_id', $user->id)->where('id', '!=', $assessment1->id)->latest()->first();
        $questions2 = $assessment2->questions()->pluck('question_text')->toArray();

        // Verifikasi bahwa terdapat perbedaan signifikan (variasi soal baru)
        $intersection = array_intersect($questions1, $questions2);

        // Dari 10 soal, mayoritas soal harus baru dan tidak sama persis
        $this->assertLessThanOrEqual(3, count($intersection), 'Sesi kedua harus menghasilkan bank soal yang berbeda dari sesi pertama.');
        $this->assertSame(10, count($questions2));
    }

    public function test_elektro_assessment_also_generates_ten_questions(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        StudentProfile::create([
            'user_id' => $user->id,
            'prodi' => 'D4 Teknik Elektro',
            'target_field' => 'elektro',
        ]);

        $response = $this->actingAs($user)->post(route('assessment.start'), ['field' => 'elektro']);
        $response->assertRedirect();

        $assessment = Assessment::where('user_id', $user->id)->latest()->first();
        $this->assertSame(10, $assessment->questions()->count());
        $this->assertSame(7, $assessment->questions()->where('type', 'hard_skill')->count());
        $this->assertSame(3, $assessment->questions()->where('type', 'soft_skill')->count());
    }
}
