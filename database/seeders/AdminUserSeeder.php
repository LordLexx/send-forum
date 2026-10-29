<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'username' => 'super_admin', // Added username just in case you have it as required on login
                'password' => Hash::make('12345678'),
                'is_admin' => true,
                'role' => 'admin', // <-- ADD THIS LINE
                'status' => 'approved',
                'address' => '123 Admin Street',
                'city' => 'Slough',
                'postcode' => 'SL1 0AA',
                'phone' => '07123 456789',
                'photo_permission' => 'no',
                'signature' => 'Super Admin',
                'date' => now()->toDateString(),
            ]
        );
    }
}
