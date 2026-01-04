<?php

namespace Database\Factories;

use App\Models\Matching;
use App\Models\User;
use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Matching>
 */
class MatchingFactory extends Factory
{
    protected $model = Matching::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $student = User::factory()->student()->create();
        $tutor = User::factory()->tutor()->has(
            \App\Models\TutorProfile::factory()
        )->create();
        $request = Request::factory()->create(['student_id' => $student->id]);

        return [
            'request_id' => $request->id,
            'student_id' => $student->id,
            'tutor_id' => $tutor->id,
            'sender_id' => fake()->randomElement([$student->id, $tutor->id]),
            'status' => 'pending',
            'message' => fake()->sentence(),
            'contact_unlocked' => false,
        ];
    }

    /**
     * Indicate that the matching is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    /**
     * Indicate that the matching is declined.
     */
    public function declined(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'declined',
        ]);
    }

    /**
     * Indicate that the matching is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the contact is unlocked.
     */
    public function unlocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'contact_unlocked' => true,
            'unlocked_at' => now(),
            'unlock_fee' => 10000,
            'payment_status' => 'completed',
        ]);
    }

    /**
     * Indicate that the student is the sender.
     */
    public function studentSender(): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_id' => $attributes['student_id'],
        ]);
    }

    /**
     * Indicate that the tutor is the sender.
     */
    public function tutorSender(): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_id' => $attributes['tutor_id'],
        ]);
    }
}
