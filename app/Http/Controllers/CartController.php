<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
  
    // Display cart page
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('food.category')
            ->get();

        $subtotal = $cartItems->sum('subtotal');
        $tax = $subtotal * 0.16; // 16% VAT
        $deliveryFee = 5000;
        $total = $subtotal + $tax + $deliveryFee;

        return view('cart.index', compact('cartItems', 'subtotal', 'tax', 'deliveryFee', 'total'));
    }

    // Add to cart (AJAX)
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'food_id' => 'required|exists:foods,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $food = Food::findOrFail($request->food_id);

        if (!$food->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'This item is currently unavailable'
            ], 400);
        }

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('food_id', $request->food_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'food_id' => $request->food_id,
                'quantity' => $request->quantity,
            ]);
        }

        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => $cartCount
        ]);
    }

    // Update cart quantity (AJAX)
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|exists:cart,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cartItem = Cart::where('id', $request->cart_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        $subtotal = $cartItem->subtotal;
        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');

        // Recalculate totals
        $allItems = Cart::where('user_id', Auth::id())->with('food')->get();
        $cartSubtotal = $allItems->sum('subtotal');
        $tax = $cartSubtotal * 0.16;
        $deliveryFee = 5000;
        $total = $cartSubtotal + $tax + $deliveryFee;

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'item_subtotal' => number_format($subtotal, 2),
            'cart_subtotal' => number_format($cartSubtotal, 2),
            'tax' => number_format($tax, 2),
            'total' => number_format($total, 2),
            'cart_count' => $cartCount
        ]);
    }

    // Remove from cart (AJAX)
    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|exists:cart,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cartItem = Cart::where('id', $request->cart_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $cartItem->delete();

        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');

        // Recalculate totals
        $allItems = Cart::where('user_id', Auth::id())->with('food')->get();
        $cartSubtotal = $allItems->sum('subtotal');
        $tax = $cartSubtotal * 0.16;
        $deliveryFee = $cartSubtotal > 0 ? 5000 : 0;
        $total = $cartSubtotal + $tax + $deliveryFee;

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_subtotal' => number_format($cartSubtotal, 2),
            'tax' => number_format($tax, 2),
            'delivery_fee' => number_format($deliveryFee, 2),
            'total' => number_format($total, 2),
            'cart_count' => $cartCount
        ]);
    }

    // Clear entire cart
    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }

    // Get cart count (AJAX)
    public function count()
    {
        $count = Cart::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}