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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('work_location_id');
            $table->timestamp('check_in_time')->nullable(); // Time when employee checked in
            $table->timestamp('check_out_time')->nullable(); // Time when employee checked out
            $table->decimal('check_in_latitude', 10, 8)->nullable(); // Latitude when checking in
            $table->decimal('check_in_longitude', 11, 8)->nullable(); // Longitude when checking in
            $table->decimal('check_out_latitude', 10, 8)->nullable(); // Latitude when checking out
            $table->decimal('check_out_longitude', 11, 8)->nullable(); // Longitude when checking out
            $table->string('check_in_image_path')->nullable(); // Path to face image during check-in
            $table->string('check_out_image_path')->nullable(); // Path to face image during check-out
            $table->boolean('is_check_in_valid')->default(false); // Is the check-in location valid?
            $table->boolean('is_check_out_valid')->default(false); // Is the check-out location valid?
            $table->boolean('is_face_recognized')->default(false); // Is the face recognized?
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
