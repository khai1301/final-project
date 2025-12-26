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
        Schema::create('student_request_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate slots for the same request
            $table->unique(['request_id', 'time_slot_id'], 'unique_request_timeslot');
            
            // Performance indexes
            $table->index('request_id');
            $table->index('time_slot_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_request_schedules');
    }
};
