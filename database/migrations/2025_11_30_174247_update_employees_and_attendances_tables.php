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
        // Update employees table
        Schema::table('employees', function (Blueprint $table) {
            // Add columns that don't exist yet
            if (!Schema::hasColumn('employees', 'full_name')) {
                $table->string('full_name')->after('employee_id');
            }
            if (!Schema::hasColumn('employees', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable();
            }
        });

        // Update attendances table - exclude foreign key constraint for now
        Schema::table('attendances', function (Blueprint $table) {
            // Add GPS coordinates and location validation fields
            if (!Schema::hasColumn('attendances', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'location_name')) {
                $table->string('location_name')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'is_valid_location')) {
                $table->boolean('is_valid_location')->default(false);
            }
            if (!Schema::hasColumn('attendances', 'clock_in_latitude')) {
                $table->decimal('clock_in_latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'clock_out_latitude')) {
                $table->decimal('clock_out_latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'clock_in_longitude')) {
                $table->decimal('clock_in_longitude', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'clock_out_longitude')) {
                $table->decimal('clock_out_longitude', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'clock_in_distance')) {
                $table->integer('clock_in_distance')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'clock_out_distance')) {
                $table->integer('clock_out_distance')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'clock_in_photo')) {
                $table->string('clock_in_photo')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'clock_out_photo')) {
                $table->string('clock_out_photo')->nullable();
            }
            if (!Schema::hasColumn('attendances', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude', 
                'location_name',
                'is_valid_location',
                'clock_in_latitude',
                'clock_out_latitude',
                'clock_in_longitude',
                'clock_out_longitude',
                'clock_in_distance',
                'clock_out_distance',
                'clock_in_photo',
                'clock_out_photo',
                'location_id'
            ]);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'address',
                'hire_date'
            ]);
        });
    }
};