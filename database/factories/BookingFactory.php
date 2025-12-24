<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
use App\Models\User;
use App\Models\Apartment;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $renter = User::where('role', 'renter')->inRandomOrder()->first();
        $apartment = Apartment::where('owner_id', '!=', $renter?->id)
            ->inRandomOrder()
            ->first();

        $start = $this->faker->dateTimeBetween('+1 days', '+1 month');
        $end   = (clone $start)->modify('+'.rand(1,7).' days');

        return [
            'user_id'      => $renter?->id,
            'apartment_id' => $apartment?->id,
            'start_date'   => $start,
            'end_date'     => $end,
            'status'       => $this->faker->randomElement([
                'pending', 'approved', 'rejected'
            ]),
        ];
    }
}
