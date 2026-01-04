<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResetMatchingDataSeeder extends Seeder
{
    /**
     * Reset only matching-related data before re-seeding
     */
    public function run(): void
    {
        $this->command->warn('⚠️  Resetting matching-related data...');
        
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables in correct order
        DB::table('payments')->truncate();
        $this->command->info('✓ Payments cleared');
        
        DB::table('matchings')->truncate();
        $this->command->info('✓ Matchings cleared');
        
        DB::table('requests')->truncate();
        $this->command->info('✓ Requests cleared');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✅ Matching data reset complete!');
        $this->command->info('→ You can now run: php artisan db:seed --class=TestDataSeeder');
    }
}
