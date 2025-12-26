<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration cleans up deprecated columns from tutor_profiles table.
     * 
     * PHASE 1: Uncomment AFTER migrating to time_slots system and updating UI
     */
    public function up(): void
    {
        Schema::table('tutor_profiles', function (Blueprint $table) {
            // ✅ Drop availability JSON - replaced by tutor_availabilities pivot
            $table->dropColumn('availability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutor_profiles', function (Blueprint $table) {
            // Restore availability column
            $table->json('availability')->nullable()->after('review_count');
        });
    }
};
