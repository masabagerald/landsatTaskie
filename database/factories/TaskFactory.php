<?php

namespace Database\Factories;

use App\Models\Category;
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
            'title'       => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status'      => 'pending',
            'priority'    => 'medium',
            'due_date'    => now()->addDays(7),
            // category_id is NOT NULL — auto-create one unless the caller overrides it
            'category_id' => Category::factory(),
        ];
    }
}
