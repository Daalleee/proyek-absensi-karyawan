<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // admin, field_leader, employee
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default roles
        \DB::table('employee_roles')->insert([
            ['name' => 'admin', 'description' => 'Administrator role with full access', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'field_leader', 'description' => 'Field leader/Supervisor role', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'employee', 'description' => 'Regular employee', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_roles');
    }
};
