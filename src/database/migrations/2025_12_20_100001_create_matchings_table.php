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
        Schema::create('matchings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'declined', 'cancelled'])->default('pending');
            $table->text('message')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['student_id', 'status']);
            $table->index(['tutor_id', 'status']);
            $table->index(['sender_id']);
            $table->index(['status']);
            
            // Prevent duplicate active requests between same student-tutor pair
            // Only one pending request allowed at a time
            $table->unique(['student_id', 'tutor_id', 'status'], 'unique_pending_match')
                  ->where('status', 'pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matchings');
    }
};
