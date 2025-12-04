<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ReportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_report_page()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Login sebagai admin
        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Akses halaman laporan
        $response = $this->get('/reports');
        $response->assertStatus(200);
    }

    public function test_admin_can_export_report()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        $employee = Employee::factory()->create([
            'user_id' => $admin->id,
            'employee_id' => 'EMP00001'
        ]);

        // Membuat beberapa data attendance
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'date' => Carbon::today(),
            'status' => 'present',
        ]);

        // Login sebagai admin
        $this->post('/login', [
            'email' => 'admin2@example.com',
            'password' => 'password',
        ]);

        // Export laporan
        $response = $this->get('/reports/export?start_date=' . Carbon::today()->format('Y-m-d') . '&end_date=' . Carbon::today()->format('Y-m-d'));
        
        // Harus merespon dengan file Excel
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_filter_report_by_employee()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'email' => 'admin3@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        $employee1 = Employee::factory()->create([
            'user_id' => $admin->id,
            'employee_id' => 'EMP00001'
        ]);

        $employee2 = Employee::factory()->create([
            'employee_id' => 'EMP00002'
        ]);

        // Membuat attendance untuk masing-masing employee
        Attendance::factory()->create([
            'employee_id' => $employee1->id,
            'date' => Carbon::today(),
            'status' => 'present',
        ]);

        Attendance::factory()->create([
            'employee_id' => $employee2->id,
            'date' => Carbon::today(),
            'status' => 'late',
        ]);

        // Login sebagai admin
        $this->post('/login', [
            'email' => 'admin3@example.com',
            'password' => 'password',
        ]);

        // Akses laporan dengan filter employee
        $response = $this->get('/reports?employee_id=' . $employee1->id);
        $response->assertStatus(200);
    }
    
    public function test_employee_cannot_access_report_page()
    {
        // Membuat user employee
        $user = User::factory()->create([
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);

        // Login sebagai employee
        $this->post('/login', [
            'email' => 'employee@example.com',
            'password' => 'password',
        ]);

        // Mencoba mengakses halaman laporan - harus ditolak
        $response = $this->get('/reports');
        $response->assertRedirect('/dashboard'); // Redirect karena middleware admin
    }
}