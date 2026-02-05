<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Breakfast', 'description' => 'Start your day right', 'display_order' => 1],
            ['name' => 'Lunch', 'description' => 'Delicious lunch options', 'display_order' => 2],
            ['name' => 'Dinner', 'description' => 'Perfect dinner meals', 'display_order' => 3],
            ['name' => 'Beverages', 'description' => 'Refreshing drinks', 'display_order' => 4],
            ['name' => 'Desserts', 'description' => 'Sweet treats', 'display_order' => 5],
            ['name' => 'Fast Food', 'description' => 'Quick and tasty', 'display_order' => 6],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}