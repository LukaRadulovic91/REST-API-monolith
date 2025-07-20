<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Software;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        static $userId = 1;
        $softwares = Software::pluck('id')->take(10)->toArray();

        return [
            'user_id' => $userId++,
            'software_id' => fake()->randomElement($softwares),
            'title' => 'Position ' . fake()->word,
            'company_name' => fake()->company(),
            'dentist_name' => 'DR. ' . fake()->name(),
            'website' => fake()->url,
            'office_address' => fake()->address(),
            'office_number' => fake()->phoneNumber(),
            'recall_time' => fake()->numberBetween(1, 3),
            'type_of_procedure' => fake()->numberBetween(1, 2),
            'vaccination_info' => fake()->numberBetween(1, 3),
            'payment_for_candidates' => fake()->numberBetween(1, 3),
            'provide_masks' => fake()->numberBetween(1, 3),
            'provide_gowns' => fake()->boolean,
            'provide_shields' => fake()->boolean,
            'digital_x_ray' => fake()->boolean,
            'ultrasonic_cavitron' => fake()->boolean,
            'free_parking' => fake()->boolean,
            'tax_deducation' => fake()->boolean,
            'sin_info' => fake()->boolean,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'suite' => 'suite '.fake()->title(),
        ];

    }
}
