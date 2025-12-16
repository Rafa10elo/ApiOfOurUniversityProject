<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(

            [
                'phone' => '0999999999',
                'first_name' => 'Super',
             'last_name'  => 'Admin',

                'password'   => Hash::make('admin123'),
                'role'       => 'admin',
                'verification_state' => 'verified',
            ]
        );
        User::factory()->create([
            'phone' => '0999999991',
            'password' => Hash::make('12345678'),
          'role' => 'renter',
        ]);
        User::factory()->create([
           'phone' => '0999999992',
            'password' => Hash::make('12345678'),
            'role' => 'owner',

        ]);

        User::factory()->count(50)->create();
        User::factory()->owner()->count(40)->create();
    }
}

