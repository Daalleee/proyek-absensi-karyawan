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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('join_date');
            $table->string('nik', 20)->nullable()->after('photo');
            $table->enum('gender', ['male', 'female'])->nullable()->after('nik');
            $table->date('birth_date')->nullable()->after('gender');
            $table->string('birth_place')->nullable()->after('birth_date');
            $table->text('address')->nullable()->after('birth_place');
            $table->string('emergency_contact')->nullable()->after('address');
            $table->string('emergency_phone')->nullable()->after('emergency_contact');
            $table->foreignId('assigned_location_id')->nullable()->after('emergency_phone')->constrained('locations')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['assigned_location_id']);
            $table->dropColumn([
                'photo', 'nik', 'gender', 'birth_date', 'birth_place',
                'address', 'emergency_contact', 'emergency_phone', 'assigned_location_id'
            ]);
        });
    }
};
