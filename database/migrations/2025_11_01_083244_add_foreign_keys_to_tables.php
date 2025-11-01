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
        // Add foreign key to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('employee_roles')->onDelete('cascade');
        });
        
        // Add foreign key to work_locations table
        Schema::table('work_locations', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('cascade');
        });
        
        // Add foreign keys to attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('work_location_id')->references('id')->on('work_locations')->onDelete('cascade');
        });
        
        // Add foreign key to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys in reverse order
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['work_location_id']);
            $table->dropForeign(['employee_id']);
        });
        
        Schema::table('work_locations', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });
        
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
        });
    }
};
