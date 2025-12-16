<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
           'phone'      => $this->faker->unique()->phoneNumber,
            'verification_state' => 'pending',

            'birth_date' => $this->faker->date(),

            'password'   => static::$password ??= Hash::make('password'),
            'role'       => 'renter',
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => 'admin',
            'verification_state' => 'verified',
        ]);
    }

    public function owner(): static
    {
        return $this->state(fn () => [
            'role' => 'owner',
        ]);
    }
}
