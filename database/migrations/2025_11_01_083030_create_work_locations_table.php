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
        Schema::create('work_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Location name
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 8); // Latitude coordinate
            $table->decimal('longitude', 11, 8); // Longitude coordinate
            $table->integer('radius'); // Radius in meters for geofence
            $table->date('date'); // Date for which this location is valid
            $table->time('start_time')->nullable(); // Work start time
            $table->time('end_time')->nullable(); // Work end time
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by'); // Created by employee
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_locations');
    }
};
