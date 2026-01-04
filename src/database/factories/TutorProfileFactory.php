<?php

namespace Database\Factories;

use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TutorProfile>
 */
class TutorProfileFactory extends Factory
{
    protected $model = TutorProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->tutor(),
            'bio' => fake()->paragraph(3),
            'education' => fake()->randomElement([
                'Đại học Bách Khoa Hà Nội',
                'Đại học Sư phạm TP.HCM',
                'Đại học Khoa học Tự nhiên',
                'Đại học Ngoại thương',
            ]),
            'experience_years' => fake()->numberBetween(1, 10),
            'hourly_rate_min' => fake()->numberBetween(100, 200) * 1000,
            'hourly_rate_max' => fake()->numberBetween(200, 500) * 1000,
            'is_approved' => true,
            'rating_avg' => fake()->randomFloat(1, 3.5, 5.0),
            'review_count' => fake()->numberBetween(0, 50),
        ];
    }

    /**
     * Indicate that the tutor profile is not approved.
     */
    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }

    /**
     * Indicate that the tutor has high rating.
     */
    public function highRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating_avg' => fake()->randomFloat(1, 4.5, 5.0),
            'review_count' => fake()->numberBetween(20, 100),
        ]);
    }

    /**
     * Indicate that the tutor is experienced.
     */
    public function experienced(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_years' => fake()->numberBetween(5, 15),
        ]);
    }
}
