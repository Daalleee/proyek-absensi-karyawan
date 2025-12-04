<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_view_their_profile()
    {
        // Membuat user dan employee
        $user = User::factory()->create([
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);
        
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'employee_id' => 'EMP00001'
        ]);

        // Login sebagai employee
        $this->post('/login', [
            'email' => 'employee@example.com',
            'password' => 'password',
        ]);

        // Akses halaman profil
        $response = $this->get('/profile');
        $response->assertStatus(200);
    }

    public function test_employee_can_update_their_profile()
    {
        // Membuat user dan employee
        $user = User::factory()->create([
            'email' => 'employee2@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);
        
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'employee_id' => 'EMP00002',
            'phone' => '123456789'
        ]);

        // Login sebagai employee
        $this->post('/login', [
            'email' => 'employee2@example.com',
            'password' => 'password',
        ]);

        // Update profil
        $response = $this->put('/profile', [
            'name' => 'Updated Name',
            'phone' => '987654321',
            'address' => 'Updated Address',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Jakarta',
            'gender' => 'male',
            'emergency_contact' => 'Emergency Contact',
            'emergency_phone' => '111222333',
        ]);

        $response->assertRedirect('/profile');
        
        // Memastikan profil telah diupdate
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
        
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'phone' => '987654321',
            'address' => 'Updated Address',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Jakarta',
            'gender' => 'male',
            'emergency_contact' => 'Emergency Contact',
            'emergency_phone' => '111222333',
        ]);
    }

    public function test_employee_can_change_password()
    {
        // Membuat user dan employee
        $user = User::factory()->create([
            'email' => 'employee3@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);
        
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'employee_id' => 'EMP00003'
        ]);

        // Login sebagai employee
        $this->post('/login', [
            'email' => 'employee3@example.com',
            'password' => 'password',
        ]);

        // Ganti password
        $response = $this->put('/profile/password', [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect('/profile');
        
        // Verifikasi bahwa password telah diubah
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword', $user->password));
    }

    public function test_employee_cannot_change_password_with_wrong_current_password()
    {
        // Membuat user dan employee
        $user = User::factory()->create([
            'email' => 'employee4@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);
        
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'employee_id' => 'EMP00004'
        ]);

        // Login sebagai employee
        $this->post('/login', [
            'email' => 'employee4@example.com',
            'password' => 'password',
        ]);

        // Ganti password dengan password lama yang salah
        $response = $this->put('/profile/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertSessionHasErrors('current_password');
    }
}