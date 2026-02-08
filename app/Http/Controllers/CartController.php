<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Controller;
class CartController extends Controller
{
    // Show cart page
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart', 'total'));
    }

    // Add item to cart
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check stock availability
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Only ' . $product->stock . ' items available.'
            ], 400);
        }

        // Check product status
        if ($product->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Product is currently unavailable.'
            ], 400);
        }

        $cart = Session::get('cart', []);

        // If product already in cart, update quantity
        if (isset($cart[$product->id])) {
            $newQuantity = $cart[$product->id]['quantity'] + $request->quantity;
            
            if ($newQuantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more. Stock limit reached.'
                ], 400);
            }

            $cart[$product->id]['quantity'] = $newQuantity;
        } else {
            // Add new product to cart
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $request->quantity,
                'image' => $product->image,
            ];
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => count($cart)
        ]);
    }

    // Update cart item quantity
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = Session::get('cart', []);

        if (!isset($cart[$product->id])) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart.'
            ], 404);
        }

        // Check stock availability
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Only ' . $product->stock . ' items available.'
            ], 400);
        }

        $cart[$product->id]['quantity'] = $request->quantity;
        Session::put('cart', $cart);

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'subtotal' => $cart[$product->id]['price'] * $request->quantity,
            'total' => $total
        ]);
    }

    // Remove item from cart
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Session::get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            Session::put('cart', $cart);

            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart',
                'cart_count' => count($cart),
                'total' => $total
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found in cart.'
        ], 404);
    }

    // Clear entire cart
    public function clear()
    {
        Session::forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    // Get cart count
    public function count()
    {
        $cart = Session::get('cart', []);
        return response()->json([
            'count' => count($cart)
        ]);
    }
}