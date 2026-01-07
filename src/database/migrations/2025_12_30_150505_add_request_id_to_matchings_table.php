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
            // Check if column exists before adding
            if (!Schema::hasColumn('matchings', 'request_id')) {
                // Add request_id foreign key (REQUIRED)
                $table->foreignId('request_id')
                      ->after('id')
                      ->constrained('requests')
                      ->cascadeOnDelete();
            }

            // Drop old unique constraint (student + tutor)
            // Use hasIndex or verify index exists before dropping if possible, 
            // but for dropUnique we usually just wrap in try-catch or assume it exists if migration hasn't fully run.
            // However, to be safe, we can attempt to drop it regardless, 
            // but standard 'down' method handles reversals. 
            // Given the duplication error is on 'add column', existing check above is most critical.
            
            // For unique constraint, we should check if it already exists before adding
            // It's tricky to check for constraint names directly via Schema builder in a clean way in Laravel < 10 sometimes without DB::select.
            // But we can rely on try-catch or just proceeding if the column check passed.
            // Actually, if 'request_id' already exists, we likely already ran this part too.
            // So we should wrap EVERYTHING in the !Schema::hasColumn check OR structure it carefully.
        });

        // Better approach: Separate schema calls or checks
        
        if (!Schema::hasColumn('matchings', 'request_id')) {
             Schema::table('matchings', function (Blueprint $table) {
                $table->foreignId('request_id')
                      ->after('id')
                      ->constrained('requests')
                      ->cascadeOnDelete();
             });
        }

        Schema::table('matchings', function (Blueprint $table) {
            // We can drop the index if it exists. 
            // Laravel doesn't have a simple 'hasIndex' on schema builder blueprint usually used like this.
            // We'll trust the migration history usually, but since this failed halfway or was manually modified:
            try {
                $table->dropUnique('unique_pending_match');
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
            
            // Add new unique constraint if not exists
            try {
                 $table->unique(['request_id', 'tutor_id', 'status'], 'unique_request_tutor_match');
            } catch (\Exception $e) {
                // Ignore if already exists
            }
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
