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
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('employee_id')->constrained()->onDelete('set null');
            $table->decimal('clock_in_latitude', 10, 8)->nullable()->after('clock_in');
            $table->decimal('clock_in_longitude', 11, 8)->nullable()->after('clock_in_latitude');
            $table->decimal('clock_out_latitude', 10, 8)->nullable()->after('clock_out');
            $table->decimal('clock_out_longitude', 11, 8)->nullable()->after('clock_out_latitude');
            $table->string('clock_in_photo')->nullable()->after('clock_out_longitude');
            $table->string('clock_out_photo')->nullable()->after('clock_in_photo');
            $table->integer('clock_in_distance')->nullable()->after('clock_out_photo');
            $table->integer('clock_out_distance')->nullable()->after('clock_in_distance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn([
                'location_id', 'clock_in_latitude', 'clock_in_longitude',
                'clock_out_latitude', 'clock_out_longitude', 'clock_in_photo',
                'clock_out_photo', 'clock_in_distance', 'clock_out_distance'
            ]);
        });
    }
};
