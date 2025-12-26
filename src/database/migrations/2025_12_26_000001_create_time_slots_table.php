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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('day_of_week')->unsigned(); // 1=Monday, 2=Tuesday, ..., 7=Sunday
            $table->time('start_time'); // e.g., '08:00:00'
            $table->time('end_time'); // e.g., '10:00:00'
            $table->integer('duration_minutes')->default(120); // 120 minutes
            $table->string('label')->nullable(); // e.g., 'Thứ 2 Sáng 08:00-10:00'
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure unique time slots
            $table->unique(['day_of_week', 'start_time', 'end_time'], 'unique_time_slot');
            
            // Performance indexes
            $table->index('day_of_week');
            $table->index('is_active');
            $table->index(['day_of_week', 'is_active'], 'idx_day_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
