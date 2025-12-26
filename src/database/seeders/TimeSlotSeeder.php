<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Days as integers: 1=Monday, 2=Tuesday, ..., 7=Sunday
        $daysOfWeek = [1, 2, 3, 4, 5, 6, 7];
        
        // Define time slots (120-minute intervals = 2 hours)
        // Format: [start_time, end_time, label_prefix]
        $timeSlots = [
            // Morning slots
            ['06:00', '08:00', 'Sáng sớm'],
            ['08:00', '10:00', 'Sáng'],
            ['10:00', '12:00', 'Trưa'],
            
            // Afternoon/Evening slots
            ['14:00', '16:00', 'Chiều'],
            ['16:00', '18:00', 'Chiều'],
            ['18:00', '20:00', 'Tối'],
            ['20:00', '22:00', 'Tối'],
        ];

        $dayLabels = [
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            7 => 'Chủ nhật',
        ];

        $data = [];
        $now = Carbon::now();

        foreach ($daysOfWeek as $dayInt) {
            foreach ($timeSlots as $slot) {
                $startTime = $slot[0];
                $endTime = $slot[1];
                $labelPrefix = $slot[2];
                
                // Calculate duration in minutes
                $start = Carbon::parse($startTime);
                $end = Carbon::parse($endTime);
                $durationMinutes = $start->diffInMinutes($end);
                
                // Create label
                $label = sprintf(
                    '%s %s (%s-%s)',
                    $dayLabels[$dayInt],
                    $labelPrefix,
                    $startTime,
                    $endTime
                );

                $data[] = [
                    'day_of_week' => $dayInt,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'duration_minutes' => $durationMinutes,
                    'label' => $label,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Insert all time slots
        DB::table('time_slots')->insert($data);

        $this->command->info('Created ' . count($data) . ' time slots (7 slots × 7 days = 49 total)');
    }
}
