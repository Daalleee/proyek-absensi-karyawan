<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AttendanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_clock_in_and_clock_out_successfully()
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
        $response = $this->post('/login', [
            'email' => 'employee@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');

        // Menguji clock in
        $response = $this->post('/attendance/clock-in', [
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'photo_base64' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/gA=='
        ]);

        $response->assertJson([
            'success' => true
        ]);

        // Memastikan attendance telah dibuat
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', Carbon::today())
            ->first();
            
        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance->clock_in);

        // Menguji clock out
        $response = $this->post('/attendance/clock-out', [
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'photo_base64' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/gA=='
        ]);

        $response->assertJson([
            'success' => true
        ]);

        // Periksa kembali attendance telah diupdate dengan clock out
        $attendance->refresh();
        $this->assertNotNull($attendance->clock_out);
    }

    public function test_employee_cannot_clock_in_twice_in_same_day()
    {
        // Membuat user dan employee
        $user = User::factory()->create([
            'email' => 'employee2@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);
        
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'employee_id' => 'EMP00002'
        ]);

        // Login sebagai employee
        $this->post('/login', [
            'email' => 'employee2@example.com',
            'password' => 'password',
        ]);

        // Lakukan clock in pertama
        $this->post('/attendance/clock-in', [
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'photo_base64' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/gA=='
        ]);

        // Lakukan clock in kedua - harus gagal
        $response = $this->post('/attendance/clock-in', [
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'photo_base64' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/gA=='
        ]);

        $response->assertJson([
            'success' => false,
            'message' => 'Anda sudah melakukan clock in hari ini'
        ]);
    }

    public function test_employee_cannot_clock_out_without_clocking_in()
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

        // Lakukan clock out tanpa clock in - harus gagal
        $response = $this->post('/attendance/clock-out', [
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'photo_base64' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/gA=='
        ]);

        $response->assertJson([
            'success' => false,
            'message' => 'Anda belum melakukan clock in hari ini'
        ]);
    }
    
    public function test_admin_can_view_attendances()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        $employee = Employee::factory()->create([
            'user_id' => $admin->id,
            'employee_id' => 'EMP00004'
        ]);

        // Login sebagai admin
        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Akses halaman attendances
        $response = $this->get('/attendances');
        $response->assertStatus(200);
    }
}