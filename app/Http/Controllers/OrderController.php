<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    // Display all orders
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $query = Order::with(['user', 'orderItems.product']);

        // Admin can see all orders, customers only their own
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('orders.index', compact('orders'));
    }

    // Show checkout page
    public function checkout()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('orders.checkout', compact('cart', 'total'));
    }

    // Process checkout and create order
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_address' => 'required|string|max:500',
            'phone' => 'required|string|max:15',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ], 400);
        }

        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first'
            ], 401);
        }

        DB::beginTransaction();

        try {
            $totalAmount = 0;

            // Verify all products and calculate total
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $product->name . ' has insufficient stock.'
                    ], 400);
                }

                if ($product->status !== 'available') {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $product->name . ' is currently unavailable.'
                    ], 400);
                }

                $totalAmount += $product->price * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'delivery_address' => $request->delivery_address,
                'phone' => $request->phone,
                'notes' => $request->notes,
            ]);

            // Create order items and update stock
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                // Reduce stock
                $product->decrement('stock', $item['quantity']);
            }

            // Clear cart
            Session::forget('cart');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_number' => $order->order_number,
                'redirect' => route('orders.show', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order. Please try again.'
            ], 500);
        }
    }

    // Show single order
    public function show(Order $order)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check authorization
        if (!$user->isAdmin() && $order->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        $order->load(['user', 'orderItems.product']);

        return view('orders.show', compact('order'));
    }

    // Update order status (Admin only)
    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }

    // Cancel order
    public function cancel(Order $order)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first'
            ], 401);
        }

        // Check authorization
        if (!$user->isAdmin() && $order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Only pending orders can be cancelled
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be cancelled'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($order->orderItems as $item) {
                $product = Product::findOrFail($item->product_id);
                $product->increment('stock', $item->quantity);
            }

            $order->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order'
            ], 500);
        }
    }

    // Delete order (Admin only)
    public function destroy(Order $order)
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }
}