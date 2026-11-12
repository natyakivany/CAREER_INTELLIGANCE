<?php

namespace Tests\Feature;

use App\Models\Occupation;
use App\Models\User;
use Database\Seeders\OccupationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_student_can_select_multiple_target_occupations(): void
    {
        $this->seed(OccupationSeeder::class);
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $occupations = Occupation::take(3)->get();
        $occIds = $occupations->pluck('id')->toArray();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Raisa Yaumil Fauziah',
            'email' => $user->email,
            'target_occupation_ids' => $occIds,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $profile = $user->fresh()->studentProfile;
        $this->assertNotNull($profile);
        $this->assertEquals($occIds, $profile->target_occupation_ids);
        $this->assertEquals($occIds[0], $profile->target_occupation_id);
        $this->assertCount(3, $profile->targetOccupations);
    }

    public function test_student_can_upload_profile_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'mahasiswa']);
        $file = UploadedFile::fake()->create('mahasiswa.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Budi Santoso',
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $profile = $user->fresh()->studentProfile;
        $this->assertNotNull($profile->avatar);
        Storage::disk('public')->assertExists($profile->avatar);
        $this->assertStringContainsString('storage/avatars/', $user->fresh()->avatar_url);
    }

    public function test_student_can_remove_profile_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'mahasiswa']);
        $file = UploadedFile::fake()->create('mahasiswa.jpg', 100, 'image/jpeg');

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Budi Santoso',
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $oldAvatar = $user->fresh()->studentProfile->avatar;
        Storage::disk('public')->assertExists($oldAvatar);

        // Remove avatar
        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Budi Santoso',
            'email' => $user->email,
            'remove_avatar' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $profile = $user->fresh()->studentProfile;
        $this->assertNull($profile->avatar);
        Storage::disk('public')->assertMissing($oldAvatar);
        $this->assertStringContainsString('ui-avatars.com', $user->fresh()->avatar_url);
    }
}
