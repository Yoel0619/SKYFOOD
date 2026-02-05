<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run(): void
    {
        $foods = [
            // Breakfast
            [
                'category_id' => 1,
                'name' => 'Pancakes with Syrup',
                'description' => 'Fluffy pancakes served with maple syrup and butter',
                'price' => 8000.00,
                'preparation_time' => 15,
                'is_vegetarian' => true,
                'calories' => 350,
            ],
            [
                'category_id' => 1,
                'name' => 'English Breakfast',
                'description' => 'Eggs, sausages, bacon, beans, and toast',
                'price' => 15000.00,
                'preparation_time' => 20,
                'is_vegetarian' => false,
                'calories' => 650,
            ],
            
            // Lunch
            [
                'category_id' => 2,
                'name' => 'Chicken Biriyani',
                'description' => 'Aromatic basmati rice with tender chicken pieces',
                'price' => 18000.00,
                'preparation_time' => 30,
                'is_vegetarian' => false,
                'calories' => 550,
            ],
            [
                'category_id' => 2,
                'name' => 'Fish and Chips',
                'description' => 'Crispy fried fish with golden fries',
                'price' => 20000.00,
                'preparation_time' => 25,
                'is_vegetarian' => false,
                'calories' => 600,
            ],
            
            // Dinner
            [
                'category_id' => 3,
                'name' => 'Grilled Steak',
                'description' => 'Juicy grilled beef steak with vegetables',
                'price' => 35000.00,
                'preparation_time' => 35,
                'is_vegetarian' => false,
                'calories' => 700,
            ],
            [
                'category_id' => 3,
                'name' => 'Pasta Carbonara',
                'description' => 'Creamy pasta with bacon and parmesan',
                'price' => 22000.00,
                'preparation_time' => 20,
                'is_vegetarian' => false,
                'calories' => 480,
            ],
            
            // Beverages
            [
                'category_id' => 4,
                'name' => 'Fresh Orange Juice',
                'description' => 'Freshly squeezed orange juice',
                'price' => 5000.00,
                'preparation_time' => 5,
                'is_vegetarian' => true,
                'calories' => 110,
            ],
            [
                'category_id' => 4,
                'name' => 'Mango Smoothie',
                'description' => 'Creamy mango smoothie with yogurt',
                'price' => 7000.00,
                'preparation_time' => 5,
                'is_vegetarian' => true,
                'calories' => 180,
            ],
            
            // Desserts
            [
                'category_id' => 5,
                'name' => 'Chocolate Cake',
                'description' => 'Rich chocolate layered cake',
                'price' => 12000.00,
                'preparation_time' => 10,
                'is_vegetarian' => true,
                'calories' => 450,
            ],
            [
                'category_id' => 5,
                'name' => 'Ice Cream Sundae',
                'description' => 'Vanilla ice cream with toppings',
                'price' => 8000.00,
                'preparation_time' => 5,
                'is_vegetarian' => true,
                'calories' => 320,
            ],
            
            // Fast Food
            [
                'category_id' => 6,
                'name' => 'Burger and Fries',
                'description' => 'Classic beef burger with crispy fries',
                'price' => 15000.00,
                'preparation_time' => 15,
                'is_vegetarian' => false,
                'calories' => 580,
            ],
            [
                'category_id' => 6,
                'name' => 'Chicken Wings',
                'description' => 'Spicy buffalo chicken wings',
                'price' => 18000.00,
                'preparation_time' => 20,
                'is_vegetarian' => false,
                'calories' => 420,
            ],
        ];

        foreach ($foods as $food) {
            Food::create($food);
        }
    }
}