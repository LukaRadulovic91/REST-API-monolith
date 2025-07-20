<?php

namespace Database\Factories;

use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Position;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = new Generator();
        static $users = 11;
        $positions = Position::pluck('id')->toArray();

        return [
            'user_id' => $users++,
            // 'position_id' => fake()->randomElement($positions),
            'registration' => 'registration '.fake()->title(),
            'expiry_date' => fake()->date(),
            'year_graduated' => fake()->year(),
            'transportation' => $faker->numberBetween(1, 7),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'suite' => 'suite '.fake()->title(),
            'school' => 'school '.fake()->title(),
        ];
    }
}
