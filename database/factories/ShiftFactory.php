<?php

namespace Database\Factories;

use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = new Generator();

        return [
            'job_ad_id' => $faker->numberBetween(1, 5),
            'start_date' => $faker->dateTimeThisYear,
            'end_date' => $faker->dateTimeThisYear,
            'start_time' => $faker->time,
            'end_time' => $faker->time,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];
    }
}
