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
            if (!Schema::hasColumn('matchings', 'contact_unlocked')) {
                $table->boolean('contact_unlocked')->default(false)->after('status');
            }
            if (!Schema::hasColumn('matchings', 'unlocked_at')) {
                $table->timestamp('unlocked_at')->nullable()->after('contact_unlocked');
            }
            if (!Schema::hasColumn('matchings', 'unlock_fee')) {
                $table->decimal('unlock_fee', 10, 2)->nullable()->after('unlocked_at');
            }
            if (!Schema::hasColumn('matchings', 'payment_status')) {
                $table->string('payment_status')->nullable()->after('unlock_fee');
            }
            if (!Schema::hasColumn('matchings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('matchings', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matchings', function (Blueprint $table) {
            $table->dropColumn([
                'contact_unlocked',
                'unlocked_at',
                'unlock_fee',
                'payment_status',
                'payment_method',
                'transaction_id'
            ]);
        });
    }
};
