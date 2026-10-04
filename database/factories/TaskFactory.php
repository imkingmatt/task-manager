<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
          'title' => fake()->sentence(3),
          'description' => fake()->paragraph(),
          'status' => 'pending',
          'due_date' => fake()->dateTimeBetween('now', '+1 month'),
          'user_id' => \App\Models\User::factory(),
      ];
    }
}
