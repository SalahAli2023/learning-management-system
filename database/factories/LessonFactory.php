<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(5),
            'file_url' => $this->faker->url(), 
            'duration' => $this->faker->numberBetween(5, 120), // 5-120 minutes
            'is_free' => $this->faker->boolean(20), // 20% chance to be free
            'order' => fake()->numberBetween(1, 20),
        ];
    }

      /**
     * Set lesson as free
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_free' => true,
        ]);
    }

    /**
     * Set lesson as paid
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_free' => false,
        ]);
    }

    /**
     * Set specific duration
     */
    public function duration(int $minutes): static
    {
        return $this->state(fn (array $attributes) => [
            'duration' => $minutes,
        ]);
    }

    /**
     * Short lesson (5-15 minutes)
     */
    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration' => $this->faker->numberBetween(5, 15),
        ]);
    }

    /**
     * Long lesson (45-120 minutes)
     */
    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration' => $this->faker->numberBetween(45, 120),
        ]);
    }

    /**
     * Lesson with file URL
     */
    public function withFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_url' => $this->faker->url(),
        ]);
    }

    /**
     * Lesson without file URL
     */
    public function withoutFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_url' => null,
        ]);
    }
}
