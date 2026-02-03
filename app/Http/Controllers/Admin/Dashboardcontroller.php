<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   
    public function index()
    {
        return view('admin.dashboard');
    }

    public function getStats()
    {
        // Total orders
        $totalOrders = Order::count();
        
        // Pending orders
        $pendingOrders = Order::where('status', 'pending')->count();
        
        // Total revenue
        $totalRevenue = Order::where('status', 'completed')
            ->sum('total_amount');
        
        // Total customers
        $totalCustomers = User::where('role', 'customer')->count();
        
        // Recent orders
        $recentOrders = Order::with(['user', 'restaurant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Orders by status
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Revenue by day (last 7 days)
        $revenueByDay = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Top selling items
        $topItems = DB::table('order_items')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->select(
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'stats' => [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'total_revenue' => $totalRevenue,
                'total_customers' => $totalCustomers,
            ],
            'recent_orders' => $recentOrders,
            'orders_by_status' => $ordersByStatus,
            'revenue_by_day' => $revenueByDay,
            'top_items' => $topItems,
        ]);
    }
}