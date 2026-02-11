<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
class CartController extends Controller
{
    /**
     * Display the cart (READ)
     */
    public function index()
    {
        $cart = session('cart', []);
        $total = 0;
        
        foreach ($cart as $id => $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return view('cart.index', compact('cart', 'total'));
    }

    /**
     * Add product to cart (CREATE)
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::find($request->product_id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Check stock availability
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Only ' . $product->stock . ' items available.'
            ], 400);
        }

        // Check if product is available
        if ($product->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'This product is currently unavailable'
            ], 400);
        }

        $cart = session('cart', []);
        
        // If product already in cart, update quantity
        if (isset($cart[$product->id])) {
            $newQuantity = $cart[$product->id]['quantity'] + $request->quantity;
            
            // Check stock for new quantity
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more. Only ' . $product->stock . ' items available.'
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

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => $product->name . ' added to cart!',
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Update cart item quantity (UPDATE)
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cart = session('cart', []);
        
        if (!isset($cart[$request->product_id])) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        }

        // Check stock availability
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Only ' . $product->stock . ' items available.'
            ], 400);
        }

        // Update quantity
        $cart[$request->product_id]['quantity'] = $request->quantity;
        session(['cart' => $cart]);

        // Calculate totals
        $subtotal = $cart[$request->product_id]['price'] * $request->quantity;
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'subtotal' => $subtotal,
            'total' => $total,
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Remove product from cart (DELETE)
     */
    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cart = session('cart', []);
        
        if (!isset($cart[$request->product_id])) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        }

        $productName = $cart[$request->product_id]['name'];
        unset($cart[$request->product_id]);
        session(['cart' => $cart]);

        // Calculate total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => $productName . ' removed from cart',
            'total' => $total,
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Clear entire cart (CLEAR ALL)
     */
    public function clear()
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'cart_count' => 0
        ]);
    }

    /**
     * Get cart count (for badge)
     */
    public function count()
    {
        $cart = session('cart', []);
        
        return response()->json([
            'count' => count($cart)
        ]);
    }

    /**
     * Get cart details (API endpoint)
     */
    public function details()
    {
        $cart = session('cart', []);
        $total = 0;
        $items = [];
        
        foreach ($cart as $id => $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
            
            $items[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'image' => $item['image'],
                'subtotal' => $subtotal
            ];
        }
        
        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => $total,
            'count' => count($cart)
        ]);
    }
}