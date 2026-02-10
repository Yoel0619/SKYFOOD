<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with full access']
        );

        $customerRole = Role::firstOrCreate(
            ['name' => 'customer'],
            ['description' => 'Regular customer']
        );
        
        $deliveryRole = Role::firstOrCreate(
            ['name' => 'delivery'],
            ['description' => 'Delivery person']
        );

        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@foodorder.com'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'phone' => '0712345678',
                'address' => 'Dar es Salaam, Tanzania',
                'status' => 'active',
            ]
        );

        // Create customer user
        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'role_id' => $customerRole->id,
                'name' => 'John Customer',
                'password' => Hash::make('password123'),
                'phone' => '0723456789',
                'address' => 'Mikocheni, Dar es Salaam',
                'status' => 'active',
            ]
        );
        
        // Create delivery person
        User::firstOrCreate(
            ['email' => 'delivery@foodorder.com'],
            [
                'role_id' => $deliveryRole->id,
                'name' => 'Juma Delivery',
                'password' => Hash::make('password123'),
                'phone' => '0734567890',
                'address' => 'Kariakoo, Dar es Salaam',
                'status' => 'active',
            ]
        );

        // Create categories with images
        $pizza = Category::firstOrCreate(
            ['name' => 'Pizza'],
            [
                'description' => 'Delicious pizzas with various toppings',
                'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=400',
                'status' => 'active'
            ]
        );

        $burgers = Category::firstOrCreate(
            ['name' => 'Burgers'],
            [
                'description' => 'Juicy burgers and sandwiches',
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400',
                'status' => 'active'
            ]
        );

        $drinks = Category::firstOrCreate(
            ['name' => 'Drinks'],
            [
                'description' => 'Refreshing beverages',
                'image' => 'https://images.unsplash.com/photo-1437418747212-8d9709afab22?w=400',
                'status' => 'active'
            ]
        );
        
        $pasta = Category::firstOrCreate(
            ['name' => 'Pasta'],
            [
                'description' => 'Italian pasta dishes',
                'image' => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400',
                'status' => 'active'
            ]
        );
        
        $desserts = Category::firstOrCreate(
            ['name' => 'Desserts'],
            [
                'description' => 'Sweet treats and desserts',
                'image' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400',
                'status' => 'active'
            ]
        );

        // Create products with images
        Product::firstOrCreate(
            ['name' => 'Margherita Pizza'],
            [
                'category_id' => $pizza->id,
                'description' => 'Classic pizza with tomato sauce, mozzarella, and fresh basil',
                'price' => 25000,
                'stock' => 50,
                'image' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=400',
                'status' => 'available'
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Pepperoni Pizza'],
            [
                'category_id' => $pizza->id,
                'description' => 'Pizza topped with pepperoni and mozzarella cheese',
                'price' => 30000,
                'stock' => 40,
                'image' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=400',
                'status' => 'available'
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Cheese Burger'],
            [
                'category_id' => $burgers->id,
                'description' => 'Beef patty with cheese, lettuce, tomato and special sauce',
                'price' => 15000,
                'stock' => 60,
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400',
                'status' => 'available'
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Chicken Burger'],
            [
                'category_id' => $burgers->id,
                'description' => 'Grilled chicken breast with lettuce and mayo',
                'price' => 18000,
                'stock' => 45,
                'image' => 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=400',
                'status' => 'available'
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Coca Cola'],
            [
                'category_id' => $drinks->id,
                'description' => 'Refreshing cola drink - 500ml bottle',
                'price' => 2000,
                'stock' => 100,
                'image' => 'https://images.unsplash.com/photo-1554866585-cd94860890b7?w=400',
                'status' => 'available'
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Fresh Orange Juice'],
            [
                'category_id' => $drinks->id,
                'description' => 'Freshly squeezed orange juice - 300ml',
                'price' => 5000,
                'stock' => 30,
                'image' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=400',
                'status' => 'available'
            ]
        );
        
        Product::firstOrCreate(
            ['name' => 'Spaghetti Carbonara'],
            [
                'category_id' => $pasta->id,
                'description' => 'Classic Italian pasta with bacon and cream sauce',
                'price' => 22000,
                'stock' => 35,
                'image' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=400',
                'status' => 'available'
            ]
        );
        
        Product::firstOrCreate(
            ['name' => 'Chocolate Cake'],
            [
                'category_id' => $desserts->id,
                'description' => 'Rich chocolate cake with chocolate frosting',
                'price' => 12000,
                'stock' => 25,
                'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400',
                'status' => 'available'
            ]
        );
    }
}