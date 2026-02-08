<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrator with full access'
        ]);

        $customerRole = Role::create([
            'name' => 'customer',
            'description' => 'Regular customer'
        ]);

        // Create Admin User
        User::create([
            'role_id' => $adminRole->id,
            'name' => 'Admin User',
            'email' => 'admin@foodorder.com',
            'password' => Hash::make('password123'),
            'phone' => '0712345678',
            'address' => 'Nairobi, Kenya',
            'status' => 'active',
        ]);

        // Create Customer User
        User::create([
            'role_id' => $customerRole->id,
            'name' => 'John Doe',
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'phone' => '0723456789',
            'address' => 'Dar es Salaam, Tanzania',
            'status' => 'active',
        ]);

        // Create Categories
        $pizza = Category::create([
            'name' => 'Pizza',
            'description' => 'Delicious Italian pizzas',
            'status' => 'active',
        ]);

        $burgers = Category::create([
            'name' => 'Burgers',
            'description' => 'Juicy beef burgers',
            'status' => 'active',
        ]);

        $drinks = Category::create([
            'name' => 'Drinks',
            'description' => 'Refreshing beverages',
            'status' => 'active',
        ]);

        // Create Products
        Product::create([
            'category_id' => $pizza->id,
            'name' => 'Margherita Pizza',
            'description' => 'Classic pizza with tomato and mozzarella',
            'price' => 15000,
            'stock' => 50,
            'status' => 'available',
        ]);

        Product::create([
            'category_id' => $pizza->id,
            'name' => 'Pepperoni Pizza',
            'description' => 'Pizza with pepperoni slices',
            'price' => 18000,
            'stock' => 40,
            'status' => 'available',
        ]);

        Product::create([
            'category_id' => $burgers->id,
            'name' => 'Beef Burger',
            'description' => 'Classic beef burger with cheese',
            'price' => 12000,
            'stock' => 60,
            'status' => 'available',
        ]);

        Product::create([
            'category_id' => $burgers->id,
            'name' => 'Chicken Burger',
            'description' => 'Grilled chicken burger',
            'price' => 10000,
            'stock' => 55,
            'status' => 'available',
        ]);

        Product::create([
            'category_id' => $drinks->id,
            'name' => 'Coca Cola',
            'description' => 'Chilled Coca Cola 500ml',
            'price' => 2000,
            'stock' => 100,
            'status' => 'available',
        ]);

        Product::create([
            'category_id' => $drinks->id,
            'name' => 'Orange Juice',
            'description' => 'Fresh orange juice',
            'price' => 3000,
            'stock' => 80,
            'status' => 'available',
        ]);
    }
}