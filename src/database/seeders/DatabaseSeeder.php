<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        
        // Call seeders in dependency order
        $this->call([
            // 1. Basic data (no dependencies)
            AdminSeeder::class,
            SubjectSeeder::class,
            EducationLevelSeeder::class,
            LearningModeSeeder::class,
            TimeSlotSeeder::class,
            
            // 2. Users (no location dependency)
            UserSeeder::class,
            
            // 3. Optional: Test data (uncomment if needed)
            // TestDataSeeder::class,
        ]);
        
        $this->command->info('');
        $this->command->info('✅ Database seeding completed!');
        $this->command->info('');
        $this->command->info('📧 Login credentials:');
        $this->command->info('Admin: admin@smarttutor.com / admin123');
        $this->command->info('Student1: student1@test.com / password123 (verified)');
        $this->command->info('Student2: student2@test.com / password123 (verified)');
        $this->command->info('Student3: student3@test.com / password123 (unverified)');
        $this->command->info('Tutor1: tutor1@test.com / password123 (verified, approved)');
        $this->command->info('Tutor2: tutor2@test.com / password123 (verified, approved)');
        $this->command->info('Tutor3: tutor3@test.com / password123 (verified, approved)');
        $this->command->info('Tutor4: tutor4@test.com / password123 (unverified, pending)');
    }
}
