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
        Schema::table('matchings', function (Blueprint $table) {
            $table->text('decline_reason')->nullable()->after('message');
            $table->text('cancel_reason')->nullable()->after('decline_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matchings', function (Blueprint $table) {
            $table->dropColumn(['decline_reason', 'cancel_reason']);
        });
    }
};
