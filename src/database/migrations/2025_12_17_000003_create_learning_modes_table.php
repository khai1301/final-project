<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('learning_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'online', 'offline', 'hybrid'
            $table->string('slug')->unique();
            $table->string('icon')->nullable(); // Bootstrap icon class
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('learning_modes');
    }
};
