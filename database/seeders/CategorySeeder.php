<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories'
            ],
            [
                'name' => 'Clothing',
                'description' => 'Clothing and apparel'
            ],
            [
                'name' => 'Books',
                'description' => 'Books and literature'
            ],
            [
                'name' => 'Toys',
                'description' => 'Toys and games'
            ],
            [
                'name' => 'Furniture',
                'description' => 'Furniture and home decor'
            ],
        ]);
    }
}
