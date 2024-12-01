<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\CategoryFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
        ]);

        User::factory(10)->create();
        \App\Models\Product::factory(50)->create();
        \App\Models\Cart::factory(20)->create();
    }
}
