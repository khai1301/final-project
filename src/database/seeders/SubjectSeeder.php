<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // Toán học & Khoa học
            ['name' => 'Toán học', 'slug' => 'toan-hoc', 'description' => 'Toán học các cấp', 'is_active' => true],
            ['name' => 'Vật lý', 'slug' => 'vat-ly', 'description' => 'Vật lý các cấp', 'is_active' => true],
            ['name' => 'Hóa học', 'slug' => 'hoa-hoc', 'description' => 'Hóa học các cấp', 'is_active' => true],
            ['name' => 'Sinh học', 'slug' => 'sinh-hoc', 'description' => 'Sinh học các cấp', 'is_active' => true],
            
            // Ngôn ngữ
            ['name' => 'Tiếng Anh', 'slug' => 'tieng-anh', 'description' => 'Tiếng Anh giao tiếp và học thuật', 'is_active' => true],
            ['name' => 'Tiếng Việt', 'slug' => 'tieng-viet', 'description' => 'Ngữ văn Tiếng Việt', 'is_active' => true],
            ['name' => 'Tiếng Trung', 'slug' => 'tieng-trung', 'description' => 'Tiếng Trung cơ bản và nâng cao', 'is_active' => true],
            ['name' => 'Tiếng Nhật', 'slug' => 'tieng-nhat', 'description' => 'Tiếng Nhật cơ bản và nâng cao', 'is_active' => true],
            ['name' => 'Tiếng Hàn', 'slug' => 'tieng-han', 'description' => 'Tiếng Hàn cơ bản và nâng cao', 'is_active' => true],
            
            // Xã hội
            ['name' => 'Lịch sử', 'slug' => 'lich-su', 'description' => 'Lịch sử Việt Nam và Thế giới', 'is_active' => true],
            ['name' => 'Địa lý', 'slug' => 'dia-ly', 'description' => 'Địa lý Việt Nam và Thế giới', 'is_active' => true],
            ['name' => 'GDCD', 'slug' => 'gdcd', 'description' => 'Giáo dục công dân', 'is_active' => true],
            
            // Tin học & Công nghệ
            ['name' => 'Tin học', 'slug' => 'tin-hoc', 'description' => 'Tin học văn phòng và lập trình', 'is_active' => true],
            ['name' => 'Lập trình', 'slug' => 'lap-trinh', 'description' => 'Lập trình các ngôn ngữ', 'is_active' => true],
            
            // Nghệ thuật
            ['name' => 'Âm nhạc', 'slug' => 'am-nhac', 'description' => 'Nhạc lý và nhạc cụ', 'is_active' => true],
            ['name' => 'Mỹ thuật', 'slug' => 'my-thuat', 'description' => 'Vẽ và thiết kế', 'is_active' => true],
            
            // Kỹ năng
            ['name' => 'Kỹ năng mềm', 'slug' => 'ky-nang-mem', 'description' => 'Giao tiếp, thuyết trình, làm việc nhóm', 'is_active' => true],
            ['name' => 'Luyện thi', 'slug' => 'luyen-thi', 'description' => 'Luyện thi THPT, Đại học, IELTS, TOEIC', 'is_active' => true],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['slug' => $subject['slug']],
                $subject
            );
        }

        $this->command->info('Subjects seeded successfully!');
    }
}
