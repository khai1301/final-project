<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TutorProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create verified students
        $students = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'student1@test.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'phone' => '0901234567',
                'is_verified' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'student2@test.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'phone' => '0901234568',
                'is_verified' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'student3@test.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'phone' => '0901234569',
                'is_verified' => false, // Unverified student for testing
                'email_verified_at' => now(),
            ],
        ];

        foreach ($students as $studentData) {
            User::firstOrCreate(
                ['email' => $studentData['email']],
                $studentData
            );
        }

        // Create verified tutors with profiles
        $tutors = [
            [
                'user' => [
                    'name' => 'Phạm Minh D',
                    'email' => 'tutor1@test.com',
                    'password' => Hash::make('password123'),
                    'role' => 'tutor',
                    'phone' => '0902234567',
                    'is_verified' => true,
                    'email_verified_at' => now(),
                ],
                'profile' => [
                    'bio' => 'Gia sư Toán học với 5 năm kinh nghiệm. Chuyên dạy THCS và THPT.',
                    'hourly_rate_min' => 150000,
                    'hourly_rate_max' => 300000,
                    'experience_years' => 5,
                    'education' => 'Cử nhân Toán học - Đại học Khoa học Tự nhiên',
                    'is_approved' => true,
                ],
                'subjects' => [1], // Toán học
            ],
            [
                'user' => [
                    'name' => 'Hoàng Thị E',
                    'email' => 'tutor2@test.com',
                    'password' => Hash::make('password123'),
                    'role' => 'tutor',
                    'phone' => '0902234568',
                    'is_verified' => true,
                    'email_verified_at' => now(),
                ],
                'profile' => [
                    'bio' => 'Gia sư Tiếng Anh IELTS 8.0. Chuyên luyện thi IELTS và giao tiếp.',
                    'hourly_rate_min' => 200000,
                    'hourly_rate_max' => 400000,
                    'experience_years' => 3,
                    'education' => 'Cử nhân Ngôn ngữ Anh - Đại học Ngoại ngữ',
                    'is_approved' => true,
                ],
                'subjects' => [5], // Tiếng Anh
            ],
            [
                'user' => [
                    'name' => 'Vũ Văn F',
                    'email' => 'tutor3@test.com',
                    'password' => Hash::make('password123'),
                    'role' => 'tutor',
                    'phone' => '0902234569',
                    'is_verified' => true,
                    'email_verified_at' => now(),
                ],
                'profile' => [
                    'bio' => 'Gia sư Vật lý - Hóa học. Giúp học sinh hiểu bản chất vấn đề.',
                    'hourly_rate_min' => 150000,
                    'hourly_rate_max' => 350000,
                    'experience_years' => 4,
                    'education' => 'Cử nhân Vật lý - Đại học Bách Khoa',
                    'is_approved' => true,
                ],
                'subjects' => [2, 3], // Vật lý, Hóa học
            ],
            [
                'user' => [
                    'name' => 'Đặng Thị G',
                    'email' => 'tutor4@test.com',
                    'password' => Hash::make('password123'),
                    'role' => 'tutor',
                    'phone' => '0902234570',
                    'is_verified' => false, // Unverified tutor for testing
                    'email_verified_at' => now(),
                ],
                'profile' => [
                    'bio' => 'Gia sư Toán - Lý. Đang chờ xác thực.',
                    'hourly_rate_min' => 100000,
                    'hourly_rate_max' => 200000,
                    'experience_years' => 1,
                    'education' => 'Sinh viên năm 4 - Đại học Sư phạm',
                    'is_approved' => false,
                ],
                'subjects' => [1, 2], // Toán, Vật lý
            ],
        ];

        foreach ($tutors as $tutorData) {
            $user = User::firstOrCreate(
                ['email' => $tutorData['user']['email']],
                $tutorData['user']
            );

            if ($user->role === 'tutor' && !$user->tutorProfile) {
                $profile = TutorProfile::create(array_merge(
                    ['user_id' => $user->id],
                    $tutorData['profile']
                ));

                // Attach subjects only if they exist
                if (isset($tutorData['subjects']) && !empty($tutorData['subjects'])) {
                    // Verify subjects exist before syncing
                    $existingSubjects = \App\Models\Subject::whereIn('id', $tutorData['subjects'])->pluck('id')->toArray();
                    if (!empty($existingSubjects)) {
                        $profile->subjects()->sync($existingSubjects);
                    } else {
                        $this->command->warn("Warning: No subjects found for tutor {$user->email}. Skipping subject assignment.");
                    }
                }
            }
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Students: student1@test.com, student2@test.com, student3@test.com (unverified)');
        $this->command->info('Tutors: tutor1@test.com, tutor2@test.com, tutor3@test.com, tutor4@test.com (unverified)');
        $this->command->info('Password for all: password123');
    }
}
