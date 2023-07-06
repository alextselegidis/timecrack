<?php

namespace Database\Factories;

use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(array_column(GenderEnum::cases(), 'value'));

        return [
            'title' => fake()->title($gender),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->phoneNumber(),
            'phone_alt' => fake()->phoneNumber(),
            'gender' => $gender,
            'street' => fake()->streetAddress(),
            'street_additional' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postcode' => fake()->postcode(),
            'country' => fake()->country(),
            'birthdate' => fake()->date(),
            'password' => '$2y$10$dlo9lsJu69cshgdTJ9exEer4WJ90AQ3rvm7Ps84fnwXX3UlfZTPoa', // Default Password: "12345678"
            'role' => RoleEnum::USER->value,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => NULL,
        ]);
    }

    /**
     * Indicate that the model is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => RoleEnum::ADMIN->value,
        ]);
    }
}
