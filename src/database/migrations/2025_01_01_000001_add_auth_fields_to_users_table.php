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
            $table->enum('role', ['student', 'tutor', 'admin'])->default('student')->after('email');
            $table->string('phone')->nullable()->unique()->after('role');
            $table->string('avatar')->nullable()->after('phone');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->timestamp('banned_at')->nullable()->after('phone_verified_at');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'avatar',
                'phone_verified_at',
                'banned_at',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
