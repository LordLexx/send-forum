<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Removed User::factory() to prevent the Faker error!

        $this->call([
            AdminUserSeeder::class,
            CategoriesSeeder::class,
            JargonTermsSeeder::class,
            //RegularUserSeeder::class,
        ]);
    }
}
