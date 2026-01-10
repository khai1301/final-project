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
        $this->command->info('🌱 Starting Comprehensive Test Data Seeder...');
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

        $this->command->info('🎓 Step 2: Ensuring education levels exist...');
        $requiredLevels = [
            ['name' => 'Lớp 10', 'order' => 1],
            ['name' => 'Lớp 11', 'order' => 2],
            ['name' => 'Lớp 12', 'order' => 3],
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

        $this->command->info('🏠 Step 3: Ensuring learning modes exist...');
        $requiredModes = [
            ['name' => 'Tại nhà', 'slug' => 'tai-nha', 'icon' => 'home'],
            ['name' => 'Online', 'slug' => 'online', 'icon' => 'laptop'],
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

        // ====== STEP 2: Get Location Data ======
        $this->command->newLine();
        $this->command->info('📍 Step 4: Getting location data...');
        
        $timeSlots = TimeSlot::all();
        $provinces = Province::take(2)->get(); // Get up to 2 provinces
        $province1 = $provinces->first();
        $province2 = $provinces->count() > 1 ? $provinces->last() : $province1;
        
        if (!$province1) {
            $this->command->error('❌ No province found. Please run location seeders first.');
            return;
        }
        
        $wards1 = Ward::where('province_code', $province1->code)->take(5)->get();
        $wards2 = Ward::where('province_code', $province2->code)->take(5)->get();

        if ($timeSlots->isEmpty() || $wards1->isEmpty()) {
            $this->command->error('❌ Missing time slots or wards. Please run basic seeders first.');
            return;
        }

        // ====== STEP 3: Create Students ======
        $this->command->newLine();
        $this->command->info('👨‍🎓 Step 5: Creating students...');
        $students = [];
        
        // Students 1-5 (Province 1)
        for ($i = 1; $i <= 5; $i++) {
            $student = User::firstOrCreate(
                ['email' => "student{$i}@test.com"],
                [
                    'name' => "Học Sinh P1-{$i}",
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'phone' => '0912345' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'province_id' => $province1->id, // Add province to user for "No Request" sorting
                ]
            );
            $students[] = $student;
            $this->command->info("  ✓ {$student->name} (Province 1)");
        }

        // Student 6 (Province 2, No Requests)
        $student6 = User::firstOrCreate(
            ['email' => "student6@test.com"],
            [
                'name' => "Học Sinh P2-NoRequest",
                'password' => Hash::make('password'),
                'role' => 'student',
                'phone' => '0912345006',
                'province_id' => $province2->id,
            ]
        );
        $this->command->info("  ✓ {$student6->name} (Province 2, No Active Requests)");


        // ====== STEP 4: Create Tutors with Profiles ======
        $this->command->newLine();
        $this->command->info('👨‍🏫 Step 6: Creating tutors...');
        
        $tutors = [];
        $tutorData = [
            // Tutors in Province 1 (Approved)
            ['name' => 'Tutor P1 A (Math)', 'subjects' => ['Toán'], 'province' => $province1, 'wards' => $wards1],
            ['name' => 'Tutor P1 B (Phys)', 'subjects' => ['Vật lý'], 'province' => $province1, 'wards' => $wards1],
            ['name' => 'Tutor P1 C (Math, Chem)', 'subjects' => ['Toán', 'Hóa học'], 'province' => $province1, 'wards' => $wards1],
            
            // Tutors in Province 2 (Approved)
            ['name' => 'Tutor P2 A (Eng)', 'subjects' => ['Tiếng Anh'], 'province' => $province2, 'wards' => $wards2],
            ['name' => 'Tutor P2 B (Lit)', 'subjects' => ['Ngữ văn'], 'province' => $province2, 'wards' => $wards2],
        ];

        foreach ($tutorData as $index => $data) {
            $tutor = User::firstOrCreate(
                ['email' => "tutor" . ($index + 1) . "@test.com"],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'tutor',
                    'phone' => '0987654' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                ]
            );
            $tutors[] = $tutor;

            $profile = TutorProfile::updateOrCreate(
                ['user_id' => $tutor->id],
                [
                    'education' => 'Đại học Test',
                    'experience_years' => rand(1, 10),
                    'hourly_rate_min' => 100000,
                    'hourly_rate_max' => 200000,
                    'bio' => 'Bio for ' . $data['name'],
                    'is_approved' => true,
                ]
            );

            // Attach subjects
            $subjectIds = Subject::whereIn('name', $data['subjects'])->pluck('id');
            $profile->subjects()->sync($subjectIds);

            // Attach teaching areas (Specific Province)
            $profile->teachingAreas()->delete();
            $profile->teachingAreas()->create([
                'province_id' => $data['province']->id,
                'ward_id' => $data['wards']->random()->id
            ]);

            $this->command->info("  ✓ {$tutor->name} (Approved)");
        }

        // Tutor 6: Unapproved (Province 1)
        $tutor6 = User::firstOrCreate(
            ['email' => "tutor6@test.com"],
            ['name' => "Tutor Unapproved", 'password' => Hash::make('password'), 'role' => 'tutor']
        );
        $profile6 = TutorProfile::updateOrCreate(
            ['user_id' => $tutor6->id],
            ['bio' => 'Waiting for approval', 'is_approved' => false]
        );
        $profile6->teachingAreas()->create(['province_id' => $province1->id]);
        $this->command->info("  ✓ Tutor 6 (Unapproved)");

        // Tutor 7: No Profile
        $tutor7 = User::firstOrCreate(
            ['email' => "tutor7@test.com"],
            ['name' => "Tutor NoProfile", 'password' => Hash::make('password'), 'role' => 'tutor']
        );
        $this->command->info("  ✓ Tutor 7 (No Profile)");


        // ====== STEP 5: Create Requests ======
        $this->command->newLine();
        $this->command->info('📝 Step 7: Creating requests...');

        // Student 1-3 (Province 1)
        foreach (array_slice($students, 0, 3) as $i => $student) {
             Request::create([
                'student_id' => $student->id,
                'title' => "Request P1 Student {$i}",
                'subject_id' => Subject::where('name', 'Toán')->first()->id, // Everyone wants Math in P1
                'province_id' => $province1->id,
                'status' => 'open',
                'description' => 'Test request description',
                'budget_min' => 100000, 'budget_max' => 200000,
                'learning_mode_id' => LearningMode::first()->id
            ]);
        }

        // Student 4-5 (Province 2 - Even though they live in P1? Let's say they want P2)
        // Actually earlier I said Students 1-5 are P1. Let's make Student 4 & 5 Requests in P2 to test mismatch logic?
        // No, keep it simple. Student 4 & 5 make requests in P2.
        foreach (array_slice($students, 3, 2) as $i => $student) {
             Request::create([
                'student_id' => $student->id,
                'title' => "Request P2 Student " . ($i+4),
                'subject_id' => Subject::where('name', 'Tiếng Anh')->first()->id,
                'province_id' => $province2->id,
                'status' => 'open',
                'description' => 'Test request P2',
                'budget_min' => 150000, 'budget_max' => 250000,
                'learning_mode_id' => LearningMode::first()->id
            ]);
        }
        $this->command->info("  ✓ Created 5 requests (3 in P1, 2 in P2)");


        // ====== STEP 6: Create Matchings (For Ranking) ======
        $this->command->newLine();
        $this->command->info('🤝 Step 8: Creating matchings...');

        // Tutor 1 (P1, Math): 3 Completed Paid Matches -> Champion
        $this->createMatching($tutors[0], $students[0], 'accepted', true);
        $this->createMatching($tutors[0], $students[1], 'accepted', true);
        $this->createMatching($tutors[0], $student6, 'accepted', true); // Student 6 from P2 hired Tutor 1 (Online maybe?)
        $this->command->info("  ✓ Tutor 1: 3 Paid Matches");

        // Tutor 2 (P1, Phys): 1 Completed Paid Match
        $this->createMatching($tutors[1], $students[2], 'accepted', true);
        $this->command->info("  ✓ Tutor 2: 1 Paid Match");

        // Tutor 3 (P1, Math/Chem): 5 Accepted but UNPAID Matches (Should not rank high)
        $this->createMatching($tutors[2], $students[0], 'accepted', false); // Unpaid
        $this->createMatching($tutors[2], $students[1], 'accepted', false);
        $this->command->info("  ✓ Tutor 3: 2 Unpaid Matches (Should not count for ranking)");

        // Tutor 4 (P2): 0 Matches
        
        $this->command->newLine();
        $this->command->info('✅ Test Data Generation Complete!');
    }

    private function createMatching($tutor, $student, $status, $paid)
    {
        // Must attach to a request? Or can be direct?
        // Current logic requires request matches?
        // Actually, matching usually links to a request.
        // I'll create dummy requests for these past matches if needed, OR just create Matching without request if nullable (it was made non-nullable recently? I should check).
        // A previous summary said "Refactor Request Matching Logic... make request_id foreign key non-nullable".
        // So I MUST create a closed request for each matching.
        
        $request = Request::create([
            'student_id' => $student->id,
            'title' => "Past Request for " . $tutor->name,
            'subject_id' => 1, // Any
            'province_id' => 1,
            'status' => 'closed', // Closed because matched
            'description' => 'History',
            'budget_min' => 100, 'budget_max' => 200,
            'learning_mode_id' => 1
        ]);

        \App\Models\Matching::create([
            'tutor_id' => $tutor->id,
            'student_id' => $student->id,
            'request_id' => $request->id,
            'status' => $status,
            'contact_unlocked' => $paid,
            'sender_id' => $student->id
        ]);
    }
}
