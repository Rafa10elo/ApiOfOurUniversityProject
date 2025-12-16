<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $renters = User::where('role', 'renter')->get();
        $apartments = Apartment::all();

        foreach ($renters as $renter) {

            $availableApartments = $apartments->where(
                'owner_id', '!=', $renter->id
            );

            if ($availableApartments->isEmpty()) {
                continue;
            }

            $apartment = $availableApartments->random();

            if (rand(0, 1) === 0) {
                continue;
            }

            $start = now()->addDays(rand(1, 20));
            $end   = (clone $start)->addDays(rand(1, 7));

            $conflict = Booking::where('apartment_id', $apartment->id)
                ->where('status', 'approved')
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end])
                        ->orWhere(function ($q2) use ($start, $end) {
                            $q2->where('start_date', '<=', $start)
                                ->where('end_date', '>=', $end);
                        });
                })
                ->exists();

            if ($conflict) {
                continue;
            }

            Booking::create([
                'user_id'      => $renter->id,
                'apartment_id' => $apartment->id,
                'start_date'   => $start,
                'end_date'     => $end,
                'status'       => rand(0, 1) ? 'approved':'pending',
            ]);
        }
    }
}
