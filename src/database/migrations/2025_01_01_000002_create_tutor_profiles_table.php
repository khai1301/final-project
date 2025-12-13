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
        Schema::create('tutor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('subjects')->nullable()->comment('["Math", "English"]');
            $table->text('education')->nullable();
            $table->integer('experience_years')->nullable();
            $table->decimal('hourly_rate_min', 10, 2)->nullable();
            $table->decimal('hourly_rate_max', 10, 2)->nullable();
            $table->json('teaching_areas')->nullable()->comment('Locations');
            $table->text('bio')->nullable();
            $table->json('certificates')->nullable()->comment('Array of URLs');
            $table->boolean('is_approved')->default(false);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->json('availability')->nullable()->comment('{"monday": ["08:00-10:00"]}');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_profiles');
    }
};
