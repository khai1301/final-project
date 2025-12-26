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
        Schema::table('users', function (Blueprint $table) {
            // Base location for all users (students and tutors)
            $table->unsignedInteger('province_id')->nullable()->after('phone');
            $table->unsignedInteger('ward_id')->nullable()->after('province_id');
            $table->string('address_detail', 500)->nullable()->after('ward_id')->comment('Street number, hamlet, etc.');
            
            // Add indexes for fast location-based queries
            $table->index('province_id');
            $table->index('ward_id');
            $table->index(['province_id', 'ward_id'], 'idx_user_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['province_id', 'ward_id']);
            $table->dropIndex(['province_id']);
            $table->dropIndex(['ward_id']);
            
            $table->dropColumn(['province_id', 'ward_id', 'address_detail']);
        });
    }
};
