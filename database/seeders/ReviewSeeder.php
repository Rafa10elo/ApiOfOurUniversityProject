<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $renters = User::where('role', 'renter')->get();
        $apartments = Apartment::all();

        foreach ($renters as $renter) {
            $apartment = $apartments->random();

            Review::firstOrCreate(
                [
                    'user_id' => $renter->id,
                    'apartment_id' => $apartment->id,
                ],
                Review::factory()->make()->toArray()
            );
        }
    }
}
