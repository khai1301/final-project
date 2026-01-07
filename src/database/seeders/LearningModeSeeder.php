<?php

namespace Database\Seeders;

use App\Models\LearningMode;
use Illuminate\Database\Seeder;

class LearningModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modes = [
            ['name' => 'Tại nhà học sinh', 'slug' => 'tai-nha-hoc-sinh', 'is_active' => true],
            ['name' => 'Tại nhà gia sư', 'slug' => 'tai-nha-gia-su', 'is_active' => true],
            ['name' => 'Online', 'slug' => 'online', 'is_active' => true],
            ['name' => 'Tại trung tâm', 'slug' => 'tai-trung-tam', 'is_active' => true],
        ];

        foreach ($modes as $mode) {
            LearningMode::firstOrCreate(
                ['slug' => $mode['slug']],
                $mode
            );
        }

        $this->command->info('Learning modes seeded successfully!');
    }
}
