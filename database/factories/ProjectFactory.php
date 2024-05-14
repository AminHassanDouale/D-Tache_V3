<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph, 
            'tags' => $this->faker->words(3, true),
            'start_date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'category_id' => $this->faker->numberBetween(1, 3), // Adjust as needed
            'status_id' => $this->faker->numberBetween(1, 5), // Adjust as needed 
            'department_id' => $this->faker->numberBetween(1, 6), // Adjust as needed
            'user_id' => $this->faker->numberBetween(1, 2), // Adjust as needed
            'priority_id' => $this->faker->numberBetween(1, 4), // Adjust as needed
        ];
    }
}
