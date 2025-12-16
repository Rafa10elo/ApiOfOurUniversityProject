<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $end   = (clone $start)->modify('+'.rand(1,10).' days');

        return [
            'start_date' => $start,
            'end_date' => $end,
            'status' => $this->faker->randomElement([
                'pending', 'approved', 'rejected'
            ]),
        ];
    }
}

