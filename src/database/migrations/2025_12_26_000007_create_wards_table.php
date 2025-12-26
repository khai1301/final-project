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
        Schema::create('wards', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->unsignedInteger('external_id')->unique()->comment('API ward_id');
            $table->string('code', 10)->unique()->comment('Official ward code');
            $table->string('name', 255)->index()->comment('Ward/Commune name');
            $table->string('type', 50)->comment('Phường, Xã, or Thị trấn');
            $table->string('province_code', 10)->index()->comment('Belongs to province code');
            $table->timestamps();
            
            // Foreign key to provinces table
            $table->foreign('province_code')->references('code')->on('provinces')->onDelete('cascade');
            
            
            // Composite index for location queries
            $table->index(['province_code', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
