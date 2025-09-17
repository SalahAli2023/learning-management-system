<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::factory(),
            'student_id' => User::factory()->create(['role' => 'student'])->id,
            'file_url' => fake()->url(),
            'grade' => fake()->randomElement([null, fake()->numberBetween(50, 100)]),
            'feedback' => fake()->boolean(70) ? fake()->sentence() : null,
        ];
    }
}
