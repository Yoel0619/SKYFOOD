<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
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
        $admin = User::firstOrCreate(
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

        // Create customer users
        $customer1 = User::firstOrCreate(
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
        
        $customer2 = User::firstOrCreate(
            ['email' => 'mary@example.com'],
            [
                'role_id' => $customerRole->id,
                'name' => 'Mary Johnson',
                'password' => Hash::make('password123'),
                'phone' => '0734567890',
                'address' => 'Masaki, Dar es Salaam',
                'status' => 'active',
            ]
        );
        
        $customer3 = User::firstOrCreate(
            ['email' => 'peter@example.com'],
            [
                'role_id' => $customerRole->id,
                'name' => 'Peter Paul',
                'password' => Hash::make('password123'),
                'phone' => '0745678901',
                'address' => 'Upanga, Dar es Salaam',
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
                'phone' => '0756789012',
                'address' => 'Kariakoo, Dar es Salaam',
                'status' => 'active',
            ]
        );

        // Create categories
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

        // Create products - 5 Food Items
        $margherita = Product::firstOrCreate(
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

        $pepperoni = Product::firstOrCreate(
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

        $cheeseBurger = Product::firstOrCreate(
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

        $chickenBurger = Product::firstOrCreate(
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

        $veggieBurger = Product::firstOrCreate(
            ['name' => 'Veggie Burger'],
            [
                'category_id' => $burgers->id,
                'description' => 'Plant-based burger with fresh vegetables',
                'price' => 16000,
                'stock' => 35,
                'image' => 'https://images.unsplash.com/photo-1520072959219-c595dc870360?w=400',
                'status' => 'available'
            ]
        );

        // Create products - 5 Drinks
        $cola = Product::firstOrCreate(
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

        $orangeJuice = Product::firstOrCreate(
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
            ['name' => 'Mango Smoothie'],
            [
                'category_id' => $drinks->id,
                'description' => 'Creamy mango smoothie - 400ml',
                'price' => 6000,
                'stock' => 25,
                'image' => 'https://images.unsplash.com/photo-1505252585461-04db1eb84625?w=400',
                'status' => 'available'
            ]
        );
        
        Product::firstOrCreate(
            ['name' => 'Mineral Water'],
            [
                'category_id' => $drinks->id,
                'description' => 'Pure mineral water - 500ml',
                'price' => 1000,
                'stock' => 150,
                'image' => 'https://images.unsplash.com/photo-1548839140-29a749e1cf4d?w=400',
                'status' => 'available'
            ]
        );
        
        Product::firstOrCreate(
            ['name' => 'Iced Coffee'],
            [
                'category_id' => $drinks->id,
                'description' => 'Cold brew coffee with ice - 350ml',
                'price' => 4000,
                'stock' => 40,
                'image' => 'https://images.unsplash.com/photo-1517487881594-2787fef5ebf7?w=400',
                'status' => 'available'
            ]
        );

        // Create 10 sample orders (5 food + 5 drinks)
        
        // Order 1: Pizza + Cola
        $order1 = Order::create([
            'user_id' => $customer1->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => 27000,
            'status' => 'pending',
            'delivery_address' => 'Mikocheni, Dar es Salaam',
            'phone' => '0723456789',
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $margherita->id,
            'quantity' => 1,
            'price' => 25000,
            'subtotal' => 25000,
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $cola->id,
            'quantity' => 1,
            'price' => 2000,
            'subtotal' => 2000,
        ]);

        // Order 2: Burger + Orange Juice
        $order2 = Order::create([
            'user_id' => $customer2->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => 20000,
            'status' => 'pending',
            'delivery_address' => 'Masaki, Dar es Salaam',
            'phone' => '0734567890',
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $cheeseBurger->id,
            'quantity' => 1,
            'price' => 15000,
            'subtotal' => 15000,
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $orangeJuice->id,
            'quantity' => 1,
            'price' => 5000,
            'subtotal' => 5000,
        ]);

        // Order 3: Pepperoni Pizza + Cola
        $order3 = Order::create([
            'user_id' => $customer3->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => 32000,
            'status' => 'pending',
            'delivery_address' => 'Upanga, Dar es Salaam',
            'phone' => '0745678901',
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $pepperoni->id,
            'quantity' => 1,
            'price' => 30000,
            'subtotal' => 30000,
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $cola->id,
            'quantity' => 1,
            'price' => 2000,
            'subtotal' => 2000,
        ]);

        // Order 4: Chicken Burger + Mango Smoothie
        $order4 = Order::create([
            'user_id' => $customer1->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => 24000,
            'status' => 'pending',
            'delivery_address' => 'Mikocheni, Dar es Salaam',
            'phone' => '0723456789',
        ]);
        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => $chickenBurger->id,
            'quantity' => 1,
            'price' => 18000,
            'subtotal' => 18000,
        ]);
        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => Product::where('name', 'Mango Smoothie')->first()->id,
            'quantity' => 1,
            'price' => 6000,
            'subtotal' => 6000,
        ]);

        // Order 5: Veggie Burger + Water
        $order5 = Order::create([
            'user_id' => $customer2->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => 17000,
            'status' => 'pending',
            'delivery_address' => 'Masaki, Dar es Salaam',
            'phone' => '0734567890',
        ]);
        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => $veggieBurger->id,
            'quantity' => 1,
            'price' => 16000,
            'subtotal' => 16000,
        ]);
        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => Product::where('name', 'Mineral Water')->first()->id,
            'quantity' => 1,
            'price' => 1000,
            'subtotal' => 1000,
        ]);

        // Order 6: 2 Pizzas + 2 Colas
        $order6 = Order::create([
            'user_id' => $customer3->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => 54000,
            'status' => 'pending',
            'delivery_address' => 'Upanga, Dar es Salaam',
            'phone' => '0745678901',
        ]);
        OrderItem::create([
            'order_id' => $order6->id,
            'product_id' => $margherita->id,
            'quantity' => 2,
            'price' => 25000,
            'subtotal' => 50000,
        ]);
        OrderItem::create([
            'order_id' => $order6->id,
            'product_id' => $cola->id,
            'quantity' => 2,
            'price' => 2000,
            'subtotal' => 4000,
        ]);

        // Order 7: Burger + Iced Coffee
        $order7 = Order::create([
            'user_id' => $customer1->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => 19000,
            'status' => 'processing',
            'delivery_address' => 'Mikocheni, Dar es Salaam',
            'phone' => '0723456789',
        ]);
        OrderItem::create([
            'order_id' => $order7->id,
            'product_id' => $cheeseBurger->id,
            'quantity' => 1,
            'price' => 15000,
            'subtotal' => 15000,
        ]);
        OrderItem::create([
            'order_id' => $order7->id,
            'product_id' => Product::where('name', 'Iced Coffee')->first()->id,
            'quantity' => 1,
            'price' => 4000,
            'subtotal' => 4000,
        ]);

        // Order 8: Pizza + Orange Juice
        $order8 = Order::create([
            'user_id' => $customer2->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => 35000,
            'status' => 'processing',
            'delivery_address' => 'Masaki, Dar es Salaam',
            'phone' => '0734567890',
        ]);
        OrderItem::create([
            'order_id' => $order8->id,
            'product_id' => $pepperoni->id,
            'quantity' => 1,
            'price' => 30000,
            'subtotal' => 30000,
        ]);
        OrderItem::create([
            'order_id' => $order8->id,
            'product_id' => $orangeJuice->id,
            'quantity' => 1,
            'price' => 5000,
            'subtotal' => 5000,
        ]);

        // Order 9: 3 Burgers + 3 Waters
        $order9 = Order::create([
            'user_id' => $customer3->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => 48000,
            'status' => 'completed',
            'delivery_address' => 'Upanga, Dar es Salaam',
            'phone' => '0745678901',
        ]);
        OrderItem::create([
            'order_id' => $order9->id,
            'product_id' => $chickenBurger->id,
            'quantity' => 3,
            'price' => 18000,
            'subtotal' => 54000,
        ]);

        // Order 10: Pizza + Smoothie + Coffee
        $order10 = Order::create([
            'user_id' => $customer1->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => 35000,
            'status' => 'completed',
            'delivery_address' => 'Mikocheni, Dar es Salaam',
            'phone' => '0723456789',
        ]);
        OrderItem::create([
            'order_id' => $order10->id,
            'product_id' => $margherita->id,
            'quantity' => 1,
            'price' => 25000,
            'subtotal' => 25000,
        ]);
        OrderItem::create([
            'order_id' => $order10->id,
            'product_id' => Product::where('name', 'Mango Smoothie')->first()->id,
            'quantity' => 1,
            'price' => 6000,
            'subtotal' => 6000,
        ]);
        OrderItem::create([
            'order_id' => $order10->id,
            'product_id' => Product::where('name', 'Iced Coffee')->first()->id,
            'quantity' => 1,
            'price' => 4000,
            'subtotal' => 4000,
        ]);
    }
}