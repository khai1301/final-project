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
        Schema::create('tutor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_profile_id')->constrained('tutor_profiles')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate slots for the same tutor
            $table->unique(['tutor_profile_id', 'time_slot_id'], 'unique_tutor_timeslot');
            
            // Performance indexes
            $table->index('tutor_profile_id');
            $table->index('time_slot_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_availabilities');
    }
};
