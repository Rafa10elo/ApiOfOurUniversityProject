<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $renters = User::where('role', 'renter')->get();
        $apartments = Apartment::all();

        foreach ($renters as $renter) {
            $renter->favorites()->syncWithoutDetaching(
                $apartments->random(rand(1, 5))->pluck('id')
            );
        }
    }
}

