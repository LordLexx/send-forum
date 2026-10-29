<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class RegularUserSeeder extends Seeder
{
    public function run(): void
    {
        // Use en_GB locale for UK postcodes and phone numbers
        $faker = Faker::create('en_GB');

        for ($i = 1; $i <= 30; $i++) {
            $name = $faker->name;

            User::create([
                'name' => $name,
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // Default password for dummy users
                'is_admin' => false,
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'postcode' => $faker->postcode,
                'phone' => $faker->phoneNumber,
                'child_name' => $faker->optional()->firstName, // Sometimes null
                'child_age' => (string) $faker->numberBetween(1, 12), // Cast to string as per migration
                'children' => null,
                'photo_permission' => $faker->randomElement(['yes', 'no']),
                'signature' => $name,
                'date' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected', 'banned']),
            ]);
        }
    }
}
