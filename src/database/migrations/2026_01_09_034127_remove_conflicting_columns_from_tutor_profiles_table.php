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
        Schema::table('tutor_profiles', function (Blueprint $table) {
            // Drop columns that conflict with relationship methods
            if (Schema::hasColumn('tutor_profiles', 'subjects')) {
                $table->dropColumn('subjects');
            }
            if (Schema::hasColumn('tutor_profiles', 'certificates')) {
                $table->dropColumn('certificates');
            }
            if (Schema::hasColumn('tutor_profiles', 'rating_avg')) {
                $table->dropColumn('rating_avg');
            }
            if (Schema::hasColumn('tutor_profiles', 'review_count')) {
                $table->dropColumn('review_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutor_profiles', function (Blueprint $table) {
            $table->json('subjects')->nullable();
            $table->json('certificates')->nullable();
            $table->decimal('rating_avg', 3, 2)->nullable();
            $table->integer('review_count')->default(0);
        });
    }
};
