<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('matchings', function (Blueprint $table) {
            // Add request_id foreign key (REQUIRED)
            $table->foreignId('request_id')
                  ->after('id')
                  ->constrained('requests')
                  ->cascadeOnDelete();
            
            // Drop old unique constraint (student + tutor)
            $table->dropUnique('unique_pending_match');
            
            // Add new unique constraint (request + tutor)
            $table->unique(['request_id', 'tutor_id', 'status'], 'unique_request_tutor_match')
                  ->where('status', 'pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matchings', function (Blueprint $table) {
            // Drop new constraint
            $table->dropUnique('unique_request_tutor_match');
            
            // Drop foreign key and column
            $table->dropForeign(['request_id']);
            $table->dropColumn('request_id');
            
            // Restore old unique constraint
            $table->unique(['student_id', 'tutor_id', 'status'], 'unique_pending_match')
                  ->where('status', 'pending');
        });
    }
};
