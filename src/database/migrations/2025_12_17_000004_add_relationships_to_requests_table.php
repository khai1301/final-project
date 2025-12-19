<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            // Add foreign key columns
            $table->foreignId('subject_id')->nullable()->after('title')->constrained('subjects')->onDelete('set null');
            $table->foreignId('education_level_id')->nullable()->after('education_level')->constrained('education_levels')->onDelete('set null');
            $table->foreignId('learning_mode_id')->nullable()->after('mode')->constrained('learning_modes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['education_level_id']);
            $table->dropForeign(['learning_mode_id']);
            
            $table->dropColumn(['subject_id', 'education_level_id', 'learning_mode_id']);
        });
    }
};
