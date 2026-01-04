<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\User;
use App\Models\Subject;
use App\Models\EducationLevel;
use App\Models\LearningMode;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    protected $model = Request::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::factory()->student(),
            'title' => fake()->randomElement([
                'Cần gia sư Toán lớp 10',
                'Tìm gia sư Vật lý',
                'Gia sư Hóa học cấp 3',
                'Dạy kèm Tiếng Anh giao tiếp',
            ]),
            'description' => fake()->paragraph(2),
            'subject_id' => Subject::inRandomOrder()->first()?->id ?? Subject::factory(),
            'education_level_id' => EducationLevel::inRandomOrder()->first()?->id ?? EducationLevel::factory(),
            'learning_mode_id' => LearningMode::inRandomOrder()->first()?->id ?? LearningMode::factory(),
            'province_id' => Province::inRandomOrder()->first()?->id,
            'ward_id' => Ward::inRandomOrder()->first()?->id,
            'budget_min' => fake()->numberBetween(100, 200) * 1000,
            'budget_max' => fake()->numberBetween(200, 400) * 1000,
            'status' => 'open',
            'address_detail' => fake()->streetAddress(),
        ];
    }

    /**
     * Indicate that the request is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }

    /**
     * Indicate that the request has a high budget.
     */
    public function highBudget(): static
    {
        return $this->state(fn (array $attributes) => [
            'budget_min' => 300000,
            'budget_max' => 500000,
        ]);
    }

    /**
     * Indicate that the request has a low budget.
     */
    public function lowBudget(): static
    {
        return $this->state(fn (array $attributes) => [
            'budget_min' => 80000,
            'budget_max' => 150000,
        ]);
    }
}
