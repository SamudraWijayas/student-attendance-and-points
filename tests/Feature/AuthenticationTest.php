<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SMK TRI SUKSES NATAR');
    }

    public function test_dashboard_redirects_unauthenticated_user_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_login_with_username_or_email(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admintests',
            'email' => 'admin@test.sch.id',
            'password' => Hash::make('password123'),
        ]);

        // Login with username
        $response = $this->post('/login', [
            'login' => 'admintests',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // Login with email
        $responseEmail = $this->post('/login', [
            'login' => 'admin@test.sch.id',
            'password' => 'password123',
        ]);
        $responseEmail->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::create([
            'name' => 'Admin Test',
            'username' => 'admintests',
            'email' => 'admin@test.sch.id',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'admintests',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_admin_can_logout_successfully(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admintests',
            'email' => 'admin@test.sch.id',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($admin);
        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}

