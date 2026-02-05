<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
   
    // Show checkout page
    public function checkout()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('food')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('menu.index')
                ->with('error', 'Your cart is empty');
        }

        $addresses = UserAddress::where('user_id', Auth::id())->get();
        $defaultAddress = $addresses->where('is_default', true)->first();

        $subtotal = $cartItems->sum('subtotal');
        $tax = $subtotal * 0.16;
        $deliveryFee = 5000;
        $total = $subtotal + $tax + $deliveryFee;

        return view('orders.checkout', compact(
            'cartItems',
            'addresses',
            'defaultAddress',
            'subtotal',
            'tax',
            'deliveryFee',
            'total'
        ));
    }

    // Place order
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_address_id' => 'required|exists:user_addresses,id',
            'payment_method' => 'required|in:cash,card,mobile_money',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify address belongs to user
        $address = UserAddress::where('id', $request->delivery_address_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cartItems = Cart::where('user_id', Auth::id())
            ->with('food')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = $cartItems->sum('subtotal');
            $tax = $subtotal * 0.16;
            $deliveryFee = 5000;
            $total = $subtotal + $tax + $deliveryFee;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'delivery_address_id' => $request->delivery_address_id,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'delivery_fee' => $deliveryFee,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cash' ? 'pending' : 'paid',
                'order_status' => 'pending',
                'special_instructions' => $request->special_instructions,
                'estimated_delivery_time' => now()->addMinutes(45),
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'food_id' => $cartItem->food_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->food->final_price,
                    'subtotal' => $cartItem->subtotal,
                ]);
            }

            // Clear cart
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'redirect' => route('orders.show', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again.'
            ], 500);
        }
    }

    // Show order details
    public function show($id)
    {
        $order = Order::with(['items.food', 'deliveryAddress', 'statusHistory.changedBy'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    // My orders page
    public function myOrders(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
            ->with('items.food')
            ->latest();

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('order_status', $request->status);
        }

        $orders = $query->paginate(10);

        return view('orders.index', compact('orders'));
    }

    // Cancel order
    public function cancel(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled'
            ], 400);
        }

        $order->cancel($request->reason, Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully'
        ]);
    }
}