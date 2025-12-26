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
        Schema::create('tutor_teaching_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_profile_id')->constrained('tutor_profiles')->onDelete('cascade');
            
            // Vietnam 2-level administrative system
            $table->unsignedInteger('province_id');
            $table->unsignedInteger('ward_id')->nullable(); // nullable = tutor teaches entire province
            
            $table->timestamps();
            
            // Prevent duplicate teaching areas per tutor
            $table->unique(
                ['tutor_profile_id', 'province_id', 'ward_id'],
                'unique_tutor_teaching_area'
            );
            
            // Individual indexes for filtering
            $table->index('tutor_profile_id');
            $table->index('province_id');
            $table->index('ward_id');
            
            // Composite index for location-based queries
            $table->index(['province_id', 'ward_id'], 'idx_teaching_area_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_teaching_areas');
    }
};
