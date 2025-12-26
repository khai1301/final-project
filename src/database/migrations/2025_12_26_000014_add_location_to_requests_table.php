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
        Schema::table('requests', function (Blueprint $table) {
            // Add location fields for requests (can override user location)
            $table->unsignedInteger('province_id')->nullable()->after('learning_mode_id');
            $table->unsignedInteger('ward_id')->nullable()->after('province_id');
            $table->string('address_detail', 500)->nullable()->after('ward_id');
            
            // Add indexes for location-based queries
            $table->index('province_id');
            $table->index('ward_id');
            $table->index(['province_id', 'ward_id'], 'idx_request_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropIndex('idx_request_location');
            $table->dropIndex(['province_id']);
            $table->dropIndex(['ward_id']);
            $table->dropColumn(['province_id', 'ward_id', 'address_detail']);
        });
    }
};
