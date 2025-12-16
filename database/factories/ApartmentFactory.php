<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'price' => $this->faker->numberBetween(200, 1000),
           'city' => $this->faker->city,
           'governorate' => $this->faker->state,
            'bedrooms' => rand(1, 5),
         'livingrooms' => rand(1, 3),
            'bathrooms' => rand(1, 3),
          'space' => rand(50, 200),
            'totalRooms' => rand(2, 8),
        ];
    }
}
