<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LeaveFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_submit_leave_request()
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

        // Submit pengajuan cuti
        $response = $this->post('/leaves', [
            'type' => 'annual',
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'reason' => 'Liburan tahunan',
        ]);

        $response->assertRedirect('/leaves');
        
        // Memastikan pengajuan cuti telah dibuat
        $this->assertDatabaseHas('leaves', [
            'employee_id' => $employee->id,
            'type' => 'annual',
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'reason' => 'Liburan tahunan',
            'status' => 'pending',
        ]);
    }

    public function test_employee_cannot_submit_past_leave_date()
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

        // Submit pengajuan cuti dengan tanggal yang sudah lewat - harus gagal
        $response = $this->post('/leaves', [
            'type' => 'sick',
            'start_date' => Carbon::yesterday()->format('Y-m-d'),
            'end_date' => Carbon::today()->format('Y-m-d'),
            'reason' => 'Sakit',
        ]);

        $response->assertSessionHasErrors('start_date');
    }

    public function test_admin_can_approve_or_reject_leave_request()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        $employee = Employee::factory()->create([
            'user_id' => $admin->id,
            'employee_id' => 'EMP00003'
        ]);

        // Membuat employee regular
        $regularUser = User::factory()->create([
            'email' => 'regular@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);
        
        $regularEmployee = Employee::factory()->create([
            'user_id' => $regularUser->id,
            'employee_id' => 'EMP00004'
        ]);

        // Membuat pengajuan cuti
        $leave = Leave::factory()->create([
            'employee_id' => $regularEmployee->id,
            'type' => 'annual',
            'start_date' => Carbon::tomorrow()->format('Y-m-d'),
            'end_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
            'reason' => 'Liburan tahunan',
            'status' => 'pending',
        ]);

        // Login sebagai admin
        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Approve pengajuan cuti
        $response = $this->post("/leaves/{$leave->id}/approve", [
            'status' => 'approved',
            'admin_notes' => 'Disetujui',
        ]);

        $response->assertRedirect();
        
        // Memastikan status telah berubah
        $this->assertDatabaseHas('leaves', [
            'id' => $leave->id,
            'status' => 'approved',
            'admin_notes' => 'Disetujui',
            'approved_by' => $admin->id,
        ]);
    }
    
    public function test_employee_can_view_their_leave_requests()
    {
        // Membuat user dan employee
        $user = User::factory()->create([
            'email' => 'employee3@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);
        
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'employee_id' => 'EMP00005'
        ]);

        // Login sebagai employee
        $this->post('/login', [
            'email' => 'employee3@example.com',
            'password' => 'password',
        ]);

        // Akses halaman pengajuan cuti
        $response = $this->get('/leaves');
        $response->assertStatus(200);
    }
}