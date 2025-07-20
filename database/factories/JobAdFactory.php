<?php

namespace Database\Factories;

use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobAd>
 */
class JobAdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = new Generator();
        $clients = Client::pluck('id')->toArray();

        return [
            'client_id' => fake()->randomElement($clients),
            'job_ad_type' => $faker->numberBetween(1, 2),
            'job_ad_status_id' => $faker->numberBetween(1, 9),
            'title' => $faker->numberBetween(1, 7),
            'job_description' => fake()->paragraph,
            'pay_rate' => $faker->numberBetween(1, 3),
            'payment_time' => $faker->numberBetween(1, 3),
            'years_experience' => $faker->numberBetween(5, 10),
            'permament_start_date' => fake()->dateTime,
//            'candidates_feedback' => fake()->paragraph,
            'client_feedback' => fake()->paragraph,
            'client_feedback_stars' => $faker->numberBetween(0, 5),
            'is_active' => fake()->boolean,
            'lunch_break' => fake()->boolean,
            'lunch_break_duration' => 30,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];
    }
}
