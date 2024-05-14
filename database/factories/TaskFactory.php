<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
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
        $date1 = "2024-04-14";
        $date2 = "2024-05-14";
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'assigned_id' => $this->faker->numberBetween(1, 2), // Adjust as needed
            'tags' => $this->faker->words(3, true),
            //'start_date' => $this->faker->date(),
            'start_date' => $this->faker->dateTimeBetween( $date1 ,  $date2),
            'due_date' =>  $this->faker->dateTimeBetween ( date('Y-m-d', strtotime($date1. ' + 5 day'))  ,  date('Y-m-d', strtotime($date2. ' + 5 day')) ),
            'category_id' => $this->faker->numberBetween(1, 3), // Adjust as needed
            'status_id' => $this->faker->numberBetween(1, 5), // Adjust as needed
            'project_id' => $this->faker->numberBetween(1, 15), // Adjust as needed
            'department_id' => $this->faker->numberBetween(1, 6), // Adjust as needed
            'user_id' => $this->faker->numberBetween(1, 2), // Adjust as needed
            'priority_id' => $this->faker->numberBetween(1, 4), // Adjust as needed

        ];
    }
}
