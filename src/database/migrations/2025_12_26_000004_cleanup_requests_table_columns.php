<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration cleans up redundant/deprecated columns from requests table.
     * 
     * PHASE 1 (SAFE - uncommented): Drop duplicate 'grade' column
     * PHASE 2 (LATER - commented): Drop string columns after FK migration
     * PHASE 3 (LATER - commented): Drop JSON schedule after time_slots migration
     */
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            // ✅ PHASE 1: Drop immediately - duplicate column
            $table->dropColumn('grade');
            
            // ✅ PHASE 2: Drop string columns - use FK columns instead
            $table->dropColumn([
                'subject',          // Use subject_id FK instead
                'education_level',  // Use education_level_id FK instead  
                'mode',             // Use learning_mode_id FK instead
                'location_type',    // Duplicate of learning_mode.slug
            ]);
            
            // ✅ PHASE 3: Drop JSON schedule - use time_slots system instead
            $table->dropColumn('schedule'); // Replaced by student_request_schedules pivot
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            // Restore all dropped columns in reverse order
            $table->json('schedule')->nullable()->after('learning_mode_id');
            $table->string('location_type', 50)->nullable()->after('status');
            $table->string('mode')->nullable()->after('skills');
            $table->string('education_level')->after('subject_id');
            $table->string('subject')->after('title');
            $table->string('grade')->nullable()->after('subject');
        });
    }
};

