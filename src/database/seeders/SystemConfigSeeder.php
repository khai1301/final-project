<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\EducationLevel;
use App\Models\LearningMode;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Subjects
        $subjects = [
            ['name' => 'Mathematics', 'description' => 'Math and related topics', 'is_active' => true],
            ['name' => 'Physics', 'description' => 'Physics and sciences', 'is_active' => true],
            ['name' => 'Chemistry', 'description' => 'Chemistry fundamentals', 'is_active' => true],
            ['name' => 'Biology', 'description' => 'Life sciences', 'is_active' => true],
            ['name' => 'English', 'description' => 'English language and literature', 'is_active' => true],
            ['name' => 'Computer Science', 'description' => 'Programming and IT', 'is_active' => true],
            ['name' => 'Piano', 'description' => 'Piano lessons', 'is_active' => true],
            ['name' => 'Guitar', 'description' => 'Guitar lessons', 'is_active' => true],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        // Seed Education Levels
        $educationLevels = [
            ['name' => 'Elementary', 'order' => 1, 'is_active' => true],
            ['name' => 'Middle School', 'order' => 2, 'is_active' => true],
            ['name' => 'High School', 'order' => 3, 'is_active' => true],
            ['name' => 'Undergraduate', 'order' => 4, 'is_active' => true],
            ['name' => 'Postgraduate', 'order' => 5, 'is_active' => true],
            ['name' => 'Professional Certification', 'order' => 6, 'is_active' => true],
            ['name' => 'Hobby / Casual', 'order' => 7, 'is_active' => true],
        ];

        foreach ($educationLevels as $level) {
            EducationLevel::create($level);
        }

        // Seed Learning Modes
        $learningModes = [
            ['name' => 'Online', 'icon' => 'bi-laptop', 'is_active' => true],
            ['name' => 'In-Person', 'icon' => 'bi-geo-alt', 'is_active' => true],
            ['name' => 'Hybrid', 'icon' => 'bi-grid', 'is_active' => true],
        ];

        foreach ($learningModes as $mode) {
            LearningMode::create($mode);
        }
    }
}
