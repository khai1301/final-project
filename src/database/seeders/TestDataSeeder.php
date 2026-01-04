<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TutorProfile;
use App\Models\Request as StudentRequest;
use App\Models\Matching;
use App\Models\Subject;
use App\Models\EducationLevel;
use App\Models\LearningMode;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create test students
        $student1 = User::create([
            'name' => 'Nguyễn Văn A (Student)',
            'email' => 'student1@student.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '0901234567',
        ]);

        $student2 = User::create([
            'name' => 'Trần Thị B (Student)',
            'email' => 'student2@student.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '0901234568',
        ]);

        // Create test tutors
        $tutor1 = User::create([
            'name' => 'Lê Văn C (Tutor)',
            'email' => 'tutor1@tutor.com',
            'password' => Hash::make('password'),
            'role' => 'tutor',
            'phone' => '0901234569',
        ]);

        $tutor2 = User::create([
            'name' => 'Phạm Thị D (Tutor)',
            'email' => 'tutor2@tutor.com',
            'password' => Hash::make('password'),
            'role' => 'tutor',
            'phone' => '0901234570',
        ]);

        // Create tutor profiles
        $subject = Subject::first() ?? Subject::create(['name' => 'Toán học', 'status' => 'active']);
        $province = Province::first();
        
        TutorProfile::create([
            'user_id' => $tutor1->id,
            'bio' => 'Giáo viên Toán có 5 năm kinh nghiệm',
            'education' => 'Đại học Sư phạm TP.HCM',
            'experience_years' => 5,
            'hourly_rate_min' => 150000,
            'hourly_rate_max' => 250000,
            'is_approved' => true,
            'rating_avg' => 4.8,
            'review_count' => 15,
        ]);

        TutorProfile::create([
            'user_id' => $tutor2->id,
            'bio' => 'Gia sư Toán - Lý chuyên nghiệp',
            'education' => 'Đại học Bách khoa',
            'experience_years' => 3,
            'hourly_rate_min' => 120000,
            'hourly_rate_max' => 200000,
            'is_approved' => true,
            'rating_avg' => 4.5,
            'review_count' => 8,
        ]);

        // Get necessary data
        $educationLevel = EducationLevel::first() ?? EducationLevel::create(['name' => 'Lớp 10', 'order' => 10, 'status' => 'active']);
        $learningMode = LearningMode::first() ?? LearningMode::create(['name' => 'Tại nhà gia sư', 'status' => 'active']);
        $ward = Ward::first();

        // Create student requests
        $request1 = StudentRequest::create([
            'student_id' => $student1->id,
            'title' => 'Cần gia sư Toán lớp 10',
            'description' => 'Con em đang học lớp 10, cần gia sư dạy Toán học',
            'subject_id' => $subject->id,
            'education_level_id' => $educationLevel->id,
            'learning_mode_id' => $learningMode->id,
            'province_id' => $province?->id,
            'ward_id' => $ward?->id,
            'budget_min' => 150000,
            'budget_max' => 200000,
            'status' => 'open',
        ]);

        $request2 = StudentRequest::create([
            'student_id' => $student1->id,
            'title' => 'Gia sư Vật lý lớp 10',
            'description' => 'Cần người dạy kèm Vật lý',
            'subject_id' => $subject->id,
            'education_level_id' => $educationLevel->id,
            'learning_mode_id' => $learningMode->id,
            'province_id' => $province?->id,
            'ward_id' => $ward?->id,
            'budget_min' => 160000,
            'budget_max' => 220000,
            'status' => 'open',
        ]);

        $request3 = StudentRequest::create([
            'student_id' => $student2->id,
            'title' => 'Tìm gia sư Toán lớp 11',
            'description' => 'Học sinh lớp 11 cần gia sư Toán',
            'subject_id' => $subject->id,
            'education_level_id' => $educationLevel->id,
            'learning_mode_id' => $learningMode->id,
            'province_id' => $province?->id,
            'ward_id' => $ward?->id,
            'budget_min' => 180000,
            'budget_max' => 250000,
            'status' => 'open',
        ]);

        // Create matchings (all with request_id)
        
        // Student1 → Tutor1 via Request1 (pending)
        Matching::create([
            'request_id' => $request1->id,
            'student_id' => $student1->id,
            'tutor_id' => $tutor1->id,
            'sender_id' => $student1->id,
            'status' => 'pending',
            'message' => 'Xin chào, tôi quan tâm đến dịch vụ gia sư của bạn',
        ]);

        // Tutor2 → Student1 via Request2 (accepted)
        Matching::create([
            'request_id' => $request2->id,
            'student_id' => $student1->id,
            'tutor_id' => $tutor2->id,
            'sender_id' => $tutor2->id,
            'status' => 'accepted',
            'message' => 'Tôi có thể dạy Vật lý cho con em',
            'contact_unlocked' => false,
        ]);

        // Student2 → Tutor1 via Request3 (accepted)  
        Matching::create([
            'request_id' => $request3->id,
            'student_id' => $student2->id,
            'tutor_id' => $tutor1->id,
            'sender_id' => $student2->id,
            'status' => 'accepted',
            'message' => 'Bạn có thể dạy Toán lớp 11 không?',
            'contact_unlocked' => false,
        ]);

        $this->command->info('✅ Test data seeded successfully!');
        $this->command->info('Students: student1@test.com, student2@test.com');
        $this->command->info('Tutors: tutor1@test.com, tutor2@test.com');
        $this->command->info('Password: password');
    }
}
