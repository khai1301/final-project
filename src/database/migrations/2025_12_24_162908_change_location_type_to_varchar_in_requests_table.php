<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Change location_type from ENUM to VARCHAR
        DB::statement("ALTER TABLE requests MODIFY COLUMN location_type VARCHAR(50) DEFAULT NULL");
    }

    public function down()
    {
        // Revert back to ENUM
        DB::statement("ALTER TABLE requests MODIFY COLUMN location_type ENUM('online', 'offline', 'hybrid') DEFAULT 'online'");
    }
};
