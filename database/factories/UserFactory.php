<?php

namespace Database\Factories;

use App\Enums\Roles;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => Roles::ADMIN,
            'administration_id' => 1,
            'first_name' => 'Marianne',
            'last_name' => 'Marshall',
            'email' => 'luka.91.rad@gmail.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$CkGj6qk3doMo0Uc7pGDtfOrjt/VNas7.BCCd3EbJzyL0OTV.uEnpC',
            'remember_token' => Str::random(10),
            'phone_number' => '+14167290417',
            'city' => fake()->city(),
            'province' => fake()->address(),
            'postal_code' => fake()->postcode(),
            'address' => fake()->streetAddress,
            'user_image_path' => fake()->imageUrl,
            'is_api_user' => fake()->boolean,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'profile_status_id' => 2,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
