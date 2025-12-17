<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('subject');
            $table->string('grade')->nullable();
            $table->string('education_level');
            $table->json('skills')->nullable();
            $table->enum('mode', ['online', 'offline']);
            $table->json('schedule');
            $table->decimal('budget_min', 8, 2);
            $table->decimal('budget_max', 8, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'locked', 'matched', 'closed', 'cancelled'])->default('open');
            $table->enum('location_type', ['online', 'offline', 'hybrid'])->default('online');
            $table->string('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('requests');
    }
};
