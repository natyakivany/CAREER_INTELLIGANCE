<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_mitra_can_authenticate_using_email_and_is_redirected_to_mitra_dashboard(): void
    {
        $mitra = User::factory()->create([
            'role' => 'mitra',
            'name' => 'PT Mitra Solusi',
        ]);

        $response = $this->post('/login', [
            'login' => $mitra->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($mitra);
        $response->assertRedirect(route('mitra.dashboard', absolute: false));
    }

    public function test_mitra_can_authenticate_using_company_name(): void
    {
        $mitra = User::factory()->create([
            'role' => 'mitra',
            'name' => 'PT Solusi Teknologi',
        ]);

        $response = $this->post('/login', [
            'login' => 'PT Solusi Teknologi',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($mitra);
        $response->assertRedirect(route('mitra.dashboard', absolute: false));
    }

    public function test_admin_can_authenticate_and_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@gmail.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_can_authenticate_using_username_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        $response = $this->post('/login', [
            'login' => 'admin',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_logging_in_with_role_type_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        $response = $this->post('/login', [
            'role_type' => 'admin',
            'login' => 'admin',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_can_log_in_freely_when_role_type_is_mahasiswa(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        $response = $this->post('/login', [
            'role_type' => 'mahasiswa',
            'login' => 'admin@gmail.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_can_log_in_freely_when_role_type_is_mitra(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        $response = $this->post('/login', [
            'role_type' => 'mitra',
            'login' => 'admin',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_visiting_student_dashboard_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_authenticated_admin_visiting_login_screen_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }
}
