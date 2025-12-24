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
            $table->boolean('contact_unlocked')->default(false)->after('status');
            $table->timestamp('unlocked_at')->nullable()->after('contact_unlocked');
            $table->decimal('unlock_fee', 10, 2)->nullable()->after('unlocked_at');
            $table->string('payment_status')->nullable()->after('unlock_fee'); // pending, completed, failed
            $table->string('payment_method')->nullable()->after('payment_status'); // vnpay, momo, etc
            $table->string('transaction_id')->nullable()->after('payment_method');
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
