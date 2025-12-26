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
            // Drop deprecated teaching_areas JSON column
            if (Schema::hasColumn('tutor_profiles', 'teaching_areas')) {
                $table->dropColumn('teaching_areas');
            }
        });
        
        Schema::table('requests', function (Blueprint $table) {
            // Drop deprecated address column (moved to users.address_detail)
            if (Schema::hasColumn('requests', 'address')) {
                $table->dropColumn('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutor_profiles', function (Blueprint $table) {
            $table->json('teaching_areas')->nullable();
        });
        
        Schema::table('requests', function (Blueprint $table) {
            $table->string('address', 500)->nullable();
        });
    }
};
