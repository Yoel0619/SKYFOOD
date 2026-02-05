<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Food;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    
    public function index()
    {
        // Statistics
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_foods' => Food::count(),
            'total_categories' => Category::count(),
        ];

        // Recent orders
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(10)
            ->get();

        // Sales by day (last 7 days)
        $salesByDay = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling foods
        $topFoods = Food::select('foods.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'foods.id', '=', 'order_items.food_id')
            ->groupBy('foods.id')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // Orders by status
        $ordersByStatus = Order::select('order_status', DB::raw('COUNT(*) as count'))
            ->groupBy('order_status')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'salesByDay',
            'topFoods',
            'ordersByStatus'
        ));
    }
}