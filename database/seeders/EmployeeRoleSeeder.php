<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeRole;

class EmployeeRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmployeeRole::create([
            'name' => 'admin',
            'description' => 'Administrator role with full access',
        ]);
        
        EmployeeRole::create([
            'name' => 'field_leader',
            'description' => 'Field leader/Supervisor role',
        ]);
        
        EmployeeRole::create([
            'name' => 'employee',
            'description' => 'Regular employee',
        ]);
    }
}
