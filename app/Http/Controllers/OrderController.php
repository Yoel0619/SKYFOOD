<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
class OrderController extends Controller
{
    /**
     * Check if current user is admin
     */
    private function isAdmin()
    {
        return Auth::check() && Auth::user()->role && Auth::user()->role->name === 'admin';
    }

    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($this->isAdmin()) {
                $query = Order::with(['user', 'orderItems.product.category']);
            } else {
                $query = Order::where('user_id', $user->id)
                             ->with(['orderItems.product.category']);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $orders = $query->orderBy('created_at', 'desc')->paginate(10);

            return view('orders.index', compact('orders'));
            
        } catch (\Exception $e) {
            Log::error('Orders Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load orders: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $products = Product::where('status', 'available')
                          ->where('stock', '>', 0)
                          ->with('category')
                          ->get();
        
        $customers = User::whereHas('role', function($q) {
            $q->where('name', 'customer');
        })->get();
        
        return view('orders.create', compact('products', 'customers'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        Log::info('Order Store Request', $request->all());
        
        $validator = Validator::make($request->all(), [
            'user_id' => $this->isAdmin() ? 'nullable|exists:users,id' : 'nullable',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:pending,processing,completed,cancelled',
            'total_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Determine user_id
            $userId = $this->isAdmin() && $request->filled('user_id') 
                ? $request->user_id 
                : Auth::id();

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_amount' => $request->total_amount,
                'status' => $request->status ?? 'pending',
                'delivery_address' => $request->delivery_address,
                'phone' => $request->phone,
                'notes' => $request->notes,
            ]);

            Log::info('Order Created', ['order_id' => $order->id, 'order_number' => $order->order_number]);

            // Create order items and update stock
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock}, Requested: {$item['quantity']}");
                }

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                Log::info('OrderItem Created', ['order_item_id' => $orderItem->id]);

                // Update stock
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();
            
            Log::info('Order Completed Successfully', ['order_id' => $order->id]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully! Order #' . $order->order_number,
                'redirect' => route('orders.show', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        try {
            // Authorization check
            if (!$this->isAdmin() && $order->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access');
            }

            $order->load(['user', 'orderItems.product.category']);
            
            return view('orders.show', compact('order'));
            
        } catch (\Exception $e) {
            Log::error('Order Show Error: ' . $e->getMessage());
            return redirect()->route('orders.index')->with('error', 'Failed to load order');
        }
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $products = Product::where('status', 'available')->with('category')->get();
        $order->load(['orderItems.product']);
        
        return view('orders.edit', compact('order', 'products'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'total_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Restore stock from old items
            foreach ($order->orderItems as $oldItem) {
                $oldItem->product->increment('stock', $oldItem->quantity);
            }

            // Delete old items
            $order->orderItems()->delete();

            // Create new order items
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            // Update order
            $order->update([
                'total_amount' => $request->total_amount,
                'status' => $request->status,
                'delivery_address' => $request->delivery_address,
                'phone' => $request->phone,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'redirect' => route('orders.show', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($order->orderItems as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Delete order (items will be cascade deleted)
            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        $cart = session('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty');
        }

        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity'],
                ];
                $total += $product->price * $item['quantity'];
            }
        }

        return view('orders.checkout', compact('cartItems', 'total'));
    }

    /**
     * Place order from checkout
     */
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cart = session('cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $totalAmount = 0;

            // Validate stock and calculate total
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if (!$product) {
                    throw new \Exception("Product not found");
                }
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
                $totalAmount += $product->price * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'delivery_address' => $request->delivery_address,
                'phone' => $request->phone,
                'notes' => $request->notes,
            ]);

            // Create order items and update stock
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            // Clear cart
            session()->forget('cart');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully! Order #' . $order->order_number,
                'redirect' => route('orders.show', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
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
            'message' => 'Order status updated to ' . $request->status
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

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
                $item->product->increment('stock', $item->quantity);
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
}