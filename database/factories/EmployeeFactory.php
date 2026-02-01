<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->first_name(),
            'last_name' => $this->faker->lastName(),
            'employment_date' => $this->faker->date('Y-m-d', 'now'),
        ];
    }
}
