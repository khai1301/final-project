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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->unsignedInteger('external_id')->unique()->comment('API province_id');
            $table->string('code', 10)->unique()->comment('Official province code');
            $table->string('name', 255)->index()->comment('Province/City name');
            $table->string('type', 50)->comment('Tỉnh or Thành phố');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
