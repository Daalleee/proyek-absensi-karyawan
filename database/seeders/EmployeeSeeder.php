<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin role
        $adminRole = EmployeeRole::where('name', 'admin')->first();
        
        // Create a default admin user
        $adminEmployee = Employee::create([
            'employee_code' => 'ADM001',
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@attendance-system.com',
            'phone' => '+6281234567890',
            'position' => 'System Administrator',
            'department' => 'IT',
            'hire_date' => now(),
            'status' => 'active',
            'role_id' => $adminRole->id,
        ]);
        
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@attendance-system.com',
            'password' => Hash::make('password'),
            'employee_id' => $adminEmployee->id,
        ]);
    }
}
