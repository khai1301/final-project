<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use Illuminate\Database\Seeder;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['name' => 'Tiểu học', 'slug' => 'tieu-hoc', 'order' => 1, 'is_active' => true],
            ['name' => 'THCS', 'slug' => 'thcs', 'order' => 2, 'is_active' => true],
            ['name' => 'THPT', 'slug' => 'thpt', 'order' => 3, 'is_active' => true],
            ['name' => 'Đại học', 'slug' => 'dai-hoc', 'order' => 4, 'is_active' => true],
            ['name' => 'Người đi làm', 'slug' => 'nguoi-di-lam', 'order' => 5, 'is_active' => true],
            ['name' => 'Khác', 'slug' => 'khac', 'order' => 6, 'is_active' => true],
        ];

        foreach ($levels as $level) {
            EducationLevel::firstOrCreate(
                ['slug' => $level['slug']],
                $level
            );
        }

        $this->command->info('Education levels seeded successfully!');
    }
}
