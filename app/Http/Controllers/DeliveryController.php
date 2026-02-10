<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class DeliveryController extends Controller
{
    /**
     * Display a listing of deliveries
     */
    public function index(Request $request)
    {
        $query = Delivery::with('order.user', 'deliveryPerson');

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('tracking_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('order', function($q) use ($request) {
                      $q->where('order_number', 'like', '%' . $request->search . '%');
                  });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $deliveries = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('deliveries.index', compact('deliveries'));
    }

    /**
     * Show the form for creating a new delivery
     */
    public function create()
    {
        $orders = Order::whereDoesntHave('delivery')->get();
        $deliveryPersons = User::whereHas('role', function($q) {
            $q->where('name', 'delivery');
        })->get();
        
        return view('deliveries.create', compact('orders', 'deliveryPersons'));
    }

    /**
     * Store a newly created delivery
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id|unique:deliveries',
            'delivery_person_id' => 'nullable|exists:users,id',
            'delivery_address' => 'required|string',
            'phone' => 'required|string',
            'status' => 'required|in:pending,assigned,picked_up,in_transit,delivered,cancelled',
            'delivery_fee' => 'required|numeric|min:0',
            'delivery_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $delivery = Delivery::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Delivery created successfully',
            'redirect' => route('deliveries.index')
        ]);
    }

    /**
     * Display the specified delivery
     */
    public function show(Delivery $delivery)
    {
        $delivery->load('order.user', 'order.orderItems.product', 'deliveryPerson');
        return view('deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified delivery
     */
    public function edit(Delivery $delivery)
    {
        $deliveryPersons = User::whereHas('role', function($q) {
            $q->where('name', 'delivery');
        })->get();
        
        return view('deliveries.edit', compact('delivery', 'deliveryPersons'));
    }

    /**
     * Update the specified delivery
     */
    public function update(Request $request, Delivery $delivery)
    {
        $validator = Validator::make($request->all(), [
            'delivery_person_id' => 'nullable|exists:users,id',
            'delivery_address' => 'required|string',
            'phone' => 'required|string',
            'status' => 'required|in:pending,assigned,picked_up,in_transit,delivered,cancelled',
            'delivery_fee' => 'required|numeric|min:0',
            'delivery_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        
        // Update timestamps based on status
        if ($request->status == 'assigned' && !$delivery->assigned_at) {
            $data['assigned_at'] = now();
        } elseif ($request->status == 'picked_up' && !$delivery->picked_up_at) {
            $data['picked_up_at'] = now();
        } elseif ($request->status == 'delivered' && !$delivery->delivered_at) {
            $data['delivered_at'] = now();
        }

        $delivery->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Delivery updated successfully',
            'redirect' => route('deliveries.index')
        ]);
    }

    /**
     * Remove the specified delivery
     */
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Delivery deleted successfully'
        ]);
    }
}