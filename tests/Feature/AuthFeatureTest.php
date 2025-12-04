<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        // Mencoba registrasi
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        
        // Memastikan user telah dibuat
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'employee',
        ]);
        
        // Memastikan employee telah dibuat
        $this->assertDatabaseHas('employees', [
            'employee_id' => 'EMP00001',
        ]);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        // Membuat user terlebih dahulu
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // Mencoba registrasi dengan email yang sama
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_login_with_valid_credentials()
    {
        // Membuat user
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mencoba login
        $response = $this->post('/login', [
            'email' => 'login@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        // Membuat user
        User::factory()->create([
            'email' => 'login2@example.com',
            'password' => Hash::make('password'),
        ]);

        // Mencoba login dengan password salah
        $response = $this->post('/login', [
            'email' => 'login2@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_logout()
    {
        // Membuat user
        $user = User::factory()->create([
            'email' => 'logout@example.com',
            'password' => Hash::make('password'),
        ]);

        // Login terlebih dahulu
        $this->post('/login', [
            'email' => 'logout@example.com',
            'password' => 'password',
        ]);

        // Cek bahwa user sedang login
        $this->assertAuthenticated();

        // Logout
        $response = $this->post('/logout');
        $response->assertRedirect('/');

        // Cek bahwa user sudah logout
        $this->assertGuest();
    }
    
    public function test_unauthenticated_user_redirected_to_login()
    {
        // Mencoba mengakses halaman yang memerlukan login tanpa login
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}