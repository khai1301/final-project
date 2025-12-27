<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TutorProfile;
use App\Models\Request;
use App\Models\Subject;
use App\Models\EducationLevel;
use App\Models\LearningMode;
use App\Models\TimeSlot;
use App\Models\Province;
use App\Models\Ward;

class AIRecommendationTestSeeder extends Seeder
{
    /**
     * Run the database seeds for testing AI recommendations.
     * This seeder creates complete test data matching actual database schema.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting AI Recommendation Test Data Seeder...');
        $this->command->newLine();

        // ====== STEP 1: Ensure Required Master Data ======
        $this->command->info('📚 Step 1: Ensuring subjects exist...');
        $requiredSubjects = [
            ['name' => 'Toán', 'description' => 'Môn Toán học'],
            ['name' => 'Vật lý', 'description' => 'Môn Vật lý'],
            ['name' => 'Hóa học', 'description' => 'Môn Hóa học'],
            ['name' => 'Sinh học', 'description' => 'Môn Sinh học'],
            ['name' => 'Ngữ văn', 'description' => 'Môn Ngữ văn'],
            ['name' => 'Tiếng Anh', 'description' => 'Môn Tiếng Anh'],
        ];
        
        foreach ($requiredSubjects as $subjectData) {
            Subject::firstOrCreate(
                ['name' => $subjectData['name']],
                [
                    'description' => $subjectData['description'],
                    'is_active' => true,
                ]
            );
        }
        $subjects = Subject::all();
        $this->command->info("  ✓ Subjects: " . $subjects->pluck('name')->implode(', '));

        $this->command->info('🎓 Step 2: Ensuring education levels exist...');
        $requiredLevels = [
            ['name' => 'Lớp 10', 'order' => 1],
            ['name' => 'Lớp 11', 'order' => 2],
            ['name' => 'Lớp 12', 'order' => 3],
            ['name' => 'Đại học', 'order' => 4],
            ['name' => 'Người đi làm', 'order' => 5],
        ];
        
        foreach ($requiredLevels as $levelData) {
            EducationLevel::firstOrCreate(
                ['name' => $levelData['name']],
                [
                    'order' => $levelData['order'],
                    'is_active' => true,
                ]
            );
        }
        $educationLevels = EducationLevel::all();
        $this->command->info("  ✓ Education levels: " . $educationLevels->pluck('name')->implode(', '));

        $this->command->info('🏠 Step 3: Ensuring learning modes exist...');
        $requiredModes = [
            ['name' => 'Tại nhà', 'slug' => 'tai-nha', 'icon' => 'home'],
            ['name' => 'Online', 'slug' => 'online', 'icon' => 'laptop'],
            ['name' => 'Tại trung tâm', 'slug' => 'trung-tam', 'icon' => 'school'],
        ];
        
        foreach ($requiredModes as $modeData) {
            LearningMode::firstOrCreate(
                ['slug' => $modeData['slug']],
                [
                    'name' => $modeData['name'],
                    'icon' => $modeData['icon'],
                    'is_active' => true,
                ]
            );
        }
        $learningModes = LearningMode::all();
        $this->command->info("  ✓ Learning modes: " . $learningModes->pluck('name')->implode(', '));

        // ====== STEP 2: Get Location Data ======
        $this->command->newLine();
        $this->command->info('📍 Step 4: Getting location data...');
        
        $timeSlots = TimeSlot::all();
        $province = Province::first();
        
        if (!$province) {
            $this->command->error('❌ No province found. Please run location seeders first.');
            return;
        }
        
        $wards = Ward::where('province_code', $province->code)->take(5)->get();

        if ($timeSlots->isEmpty() || $wards->isEmpty()) {
            $this->command->error('❌ Missing time slots or wards. Please run basic seeders first.');
            return;
        }
        
        $this->command->info("  ✓ Province: {$province->name}");
        $this->command->info("  ✓ Wards: {$wards->count()}");
        $this->command->info("  ✓ Time slots: {$timeSlots->count()}");

        // ====== STEP 3: Create Students ======
        $this->command->newLine();
        $this->command->info('�‍🎓 Step 5: Creating students...');
        $students = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $student = User::firstOrCreate(
                ['email' => "student{$i}@test.com"],
                [
                    'name' => "Học Sinh Test {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'phone' => '0912345' . str_pad($i, 3, '0', STR_PAD_LEFT),
                ]
            );
            $students[] = $student;
            $this->command->info("  ✓ {$student->name}");
        }

        // ====== STEP 4: Create Tutors with Profiles ======
        $this->command->newLine();
        $this->command->info('👨‍🏫 Step 6: Creating tutors with profiles...');
        
        $tutorData = [
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'tutor1@test.com',
                'education' => 'Thạc sĩ Toán học - Đại học Bách Khoa',
                'experience' => 5,
                'rate_min' => 150000,
                'rate_max' => 250000,
                'bio' => 'Chuyên dạy Toán THPT, có 5 năm kinh nghiệm. Học sinh của tôi đã đạt nhiều giải thưởng học sinh giỏi quốc gia.',
                'subjects' => ['Toán', 'Vật lý'],
            ],
            [
                'name' => 'Trần Thị Bích',
                'email' => 'tutor2@test.com',
                'education' => 'Cử nhân Ngữ văn - Đại học Sư phạm',
                'experience' => 3,
                'rate_min' => 100000,
                'rate_max' => 180000,
                'bio' => 'Giáo viên Ngữ văn nhiệt huyết, đã có 3 năm kinh nghiệm giảng dạy. Chuyên luyện thi THPT Quốc gia môn Văn.',
                'subjects' => ['Ngữ văn'],
            ],
            [
                'name' => 'Lê Minh Công',
                'email' => 'tutor3@test.com',
                'education' => 'Thạc sĩ Hóa học - ĐH Khoa học Tự nhiên',
                'experience' => 7,
                'rate_min' => 200000,
                'rate_max' => 300000,
                'bio' => 'Chuyên gia Hóa học với 7 năm kinh nghiệm. Đã hướng dẫn nhiều học sinh đạt điểm 9-10 môn Hóa.',
                'subjects' => ['Hóa học', 'Sinh học'],
            ],
            [
                'name' => 'Phạm Thu Hà',
                'email' => 'tutor4@test.com',
                'education' => 'Thạc sĩ Ngôn ngữ Anh - ĐH Ngoại ngữ',
                'experience' => 4,
                'rate_min' => 120000,
                'rate_max' => 200000,
                'bio' => 'Giảng viên Tiếng Anh với IELTS 8.0. Chuyên luyện thi IELTS, TOEFL và Tiếng Anh giao tiếp.',
                'subjects' => ['Tiếng Anh'],
            ],
            [
                'name' => 'Hoàng Quốc Dũng',
                'email' => 'tutor5@test.com',
                'education' => 'Tiến sĩ Vật lý - ĐH Bách Khoa',
                'experience' => 10,
                'rate_min' => 250000,
                'rate_max' => 400000,
                'bio' => 'Giáo sư Vật lý với hơn 10 năm kinh nghiệm. Chuyên ôn thi Olympic Vật lý và THPT Quốc gia.',
                'subjects' => ['Vật lý', 'Toán'],
            ],
        ];

        foreach ($tutorData as $index => $data) {
            $tutor = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'tutor',
                    'phone' => '0987654' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                ]
            );

            $profile = TutorProfile::updateOrCreate(
                ['user_id' => $tutor->id],
                [
                    'education' => $data['education'],
                    'experience_years' => $data['experience'],
                    'hourly_rate_min' => $data['rate_min'],
                    'hourly_rate_max' => $data['rate_max'],
                    'bio' => $data['bio'],
                    'is_approved' => true,
                    'rating_avg' => rand(40, 50) / 10,
                    'review_count' => rand(10, 50),
                ]
            );

            // Attach subjects
            $subjectIds = Subject::whereIn('name', $data['subjects'])->pluck('id');
            $profile->subjects()->sync($subjectIds);

            // Attach time slots
            $randomSlots = $timeSlots->random(min(5, $timeSlots->count()))->pluck('id');
            $profile->availableTimeSlots()->sync($randomSlots);

            // Attach teaching areas
            $profile->teachingAreas()->delete();
            foreach ($wards->random(min(3, $wards->count())) as $ward) {
                $profile->teachingAreas()->firstOrCreate([
                    'ward_id' => $ward->id,
                    'province_id' => $province->id,
                ]);
            }

            $this->command->info("  ✓ {$tutor->name}");
        }

        // ====== STEP 5: Create Student Requests ======
        $this->command->newLine();
        $this->command->info('📝 Step 7: Creating student requests...');
        
        $requestData = [
            [
                'title' => 'Cần gia sư Toán lớp 12',
                'subject' => 'Toán',
                'level' => 'Lớp 12',
                'description' => 'Em đang học lớp 12, cần gia sư dạy Toán để ôn thi THPT Quốc gia. Em cần tập trung vào phần Giải tích và Hình học không gian.',
                'budget_min' => 120000,
                'budget_max' => 200000,
            ],
            [
                'title' => 'Tìm gia sư Tiếng Anh giao tiếp',
                'subject' => 'Tiếng Anh',
                'level' => 'Lớp 11',
                'description' => 'Em muốn cải thiện kỹ năng giao tiếp Tiếng Anh. Mục tiêu là có thể nói chuyện tự nhiên và đạt IELTS 6.5.',
                'budget_min' => 100000,
                'budget_max' => 180000,
            ],
            [
                'title' => 'Gia sư Hóa học lớp 10',
                'subject' => 'Hóa học',
                'level' => 'Lớp 10',
                'description' => 'Em cần học Hóa học cơ bản lớp 10. Em còn yếu phần cân bằng hóa học và tính toán mol.',
                'budget_min' => 80000,
                'budget_max' => 150000,
            ],
            [
                'title' => 'Cần gia sư Vật lý THPT',
                'subject' => 'Vật lý',
                'level' => 'Lớp 11',
                'description' => 'Em cần gia sư dạy Vật lý lớp 11, đặc biệt là phần Điện học và Dao động. Em đang chuẩn bị thi học sinh giỏi.',
                'budget_min' => 200000,
                'budget_max' => 350000,
            ],
            [
                'title' => 'Gia sư Ngữ văn lớp 12',
                'subject' => 'Ngữ văn',
                'level' => 'Lớp 12',
                'description' => 'Em đang ôn thi THPT môn Văn. Em cần giúp đỡ về phần làm bài văn nghị luận xã hội và nghị luận văn học.',
                'budget_min' => 90000,
                'budget_max' => 160000,
            ],
        ];

        foreach ($requestData as $index => $data) {
            $student = $students[$index];
            $subject = Subject::where('name', $data['subject'])->first();
            $level = EducationLevel::where('name', $data['level'])->first();
            $learningMode = LearningMode::where('slug', 'tai-nha')->first();
            $ward = $wards->random();

            // Delete old requests
            Request::where('student_id', $student->id)->delete();

            $request = Request::create([
                'student_id' => $student->id,
                'title' => $data['title'],
                'subject_id' => $subject->id,
                'education_level_id' => $level->id,
                'learning_mode_id' => $learningMode->id,
                'description' => $data['description'],
                'budget_min' => $data['budget_min'],
                'budget_max' => $data['budget_max'],
                'province_id' => $province->id,
                'ward_id' => $ward->id,
                'address_detail' => 'Địa chỉ chi tiết sẽ thảo luận sau',
                'status' => 'open',
            ]);

            // Attach time slots
            $randomSlots = $timeSlots->random(min(3, $timeSlots->count()))->pluck('id');
            $request->timeSlots()->attach($randomSlots);

            $this->command->info("  ✓ {$request->title}");
        }

        // ====== SUMMARY ======
        $this->command->newLine();
        $this->command->info('✅ Test data created successfully!');
        $this->command->newLine();
        $this->command->info('📊 Summary:');
        $this->command->info("  • Students: 5");
        $this->command->info("  • Tutors: 5 (all approved)");
        $this->command->info("  • Open Requests: 5");
        $this->command->newLine();
        $this->command->info('🔐 Login Credentials:');
        $this->command->info("  Students: student1@test.com - student5@test.com");
        $this->command->info("  Tutors: tutor1@test.com - tutor5@test.com");
        $this->command->info("  Password: password");
        $this->command->newLine();
        $this->command->info('🧪 Next steps:');
        $this->command->info('  1. Login as student1@test.com');
        $this->command->info('  2. Go to homepage /');
        $this->command->info('  3. See "Gợi Ý Dành Riêng Cho Bạn" with AI recommendations');
        $this->command->newLine();
    }
}
