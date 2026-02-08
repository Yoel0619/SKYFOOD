<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Routing\Controller;
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            // Admin Dashboard
            $stats = [
                'total_users' => User::where('role_id', '!=', $user->role_id)->count(),
                'total_products' => Product::count(),
                'total_orders' => Order::count(),
                'total_categories' => Category::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'completed_orders' => Order::where('status', 'completed')->count(),
                'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            ];

            $recent_orders = Order::with(['user', 'orderItems.product'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return view('dashboard.admin', compact('stats', 'recent_orders'));
        } else {
            // Customer Dashboard
            $stats = [
                'total_orders' => Order::where('user_id', $user->id)->count(),
                'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
                'completed_orders' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
                'total_spent' => Order::where('user_id', $user->id)->where('status', 'completed')->sum('total_amount'),
            ];

            $recent_orders = Order::with(['orderItems.product'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return view('dashboard.customer', compact('stats', 'recent_orders'));
        }
    }
}