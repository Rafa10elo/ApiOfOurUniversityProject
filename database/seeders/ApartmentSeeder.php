<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApartmentSeeder extends Seeder
{
    public function run(): void
    {
        $owners = User::where('role', 'owner')->get();

        foreach ($owners as $owner) {
            Apartment::factory(rand(1, 5))->create([
                'owner_id' => $owner->id,
            ]);
        }
    }
}
