<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@foodorder.com',
            'phone' => '+255712345678',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Manager User
        User::create([
            'name' => 'Restaurant Manager',
            'email' => 'manager@foodorder.com',
            'phone' => '+255723456789',
            'password' => Hash::make('Manager@123'),
            'role' => 'manager',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Delivery Person
        User::create([
            'name' => 'John Delivery',
            'email' => 'delivery@foodorder.com',
            'phone' => '+255734567890',
            'password' => Hash::make('Delivery@123'),
            'role' => 'delivery',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Customer
        User::create([
            'name' => 'Jane Customer',
            'email' => 'customer@foodorder.com',
            'phone' => '+255745678901',
            'password' => Hash::make('Customer@123'),
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}