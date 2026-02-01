<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        $faker = \Faker\Factory::create('ja_JP');
        $dob = $faker->dateTimeBetween('-12 years', '-3 years')->format('Y-m-d');

        return [
            'user_id' => User::factory(),

            'age_subcategory_id' => 1, // fix seeding here, then figure out how to seed enrolments.

            'name_hiragana' => $faker->kanaName(),
            'first_name_ja' => $faker->firstKanaName(),
            'last_name_ja' => $faker->lastKanaName(),
            'first_name_en' => $faker->firstName(),
            'last_name_en' => $faker->lastKanaName(),

            'employee_note' => $faker->sentence(10),

            'enrollment_date' => $faker->dateTimeBetween('-6 months', 'now'),
            'date_of_birth' => $dob,

            'override_allowed' => false,
            'capacity_weight' => 1,
            'monthly_reschedule_limit' => 1,
        ];
    }

    public function troublesome(): static
    {
        return $this->state(fn () => [
            'capacity_weight' => 2,
        ]);
    }

    public function overrideAllowed(): static
    {
        return $this->state(fn () => [
            'override_allowed' => true,
        ]);
    }
}
