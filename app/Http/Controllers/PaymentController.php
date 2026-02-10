<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
class PaymentController extends Controller
{
    /**
     * Display a listing of payments (READ)
     */
    public function index(Request $request)
    {
        $query = Payment::with('order.user');

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('order', function($q) use ($request) {
                      $q->where('order_number', 'like', '%' . $request->search . '%');
                  });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('method') && $request->method != '') {
            $query->where('payment_method', $request->method);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment (CREATE - Form)
     */
    public function create()
    {
        $orders = Order::whereDoesntHave('payment')->get();
        return view('payments.create', compact('orders'));
    }

    /**
     * Store a newly created payment (CREATE - Store)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id|unique:payments',
            'payment_method' => 'required|in:cash,mpesa,card,bank_transfer',
            'transaction_id' => 'nullable|string|unique:payments',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,completed,failed,refunded',
            'payment_details' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $payment = Payment::create([
            'order_id' => $request->order_id,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'amount' => $request->amount,
            'status' => $request->status,
            'payment_details' => $request->payment_details,
            'paid_at' => $request->status == 'completed' ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'redirect' => route('payments.index')
        ]);
    }

    /**
     * Display the specified payment (SHOW)
     */
    public function show(Payment $payment)
    {
        $payment->load('order.user', 'order.orderItems.product');
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment (UPDATE - Form)
     */
    public function edit(Payment $payment)
    {
        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified payment (UPDATE - Store)
     */
    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:cash,mpesa,card,bank_transfer',
            'transaction_id' => 'nullable|string|unique:payments,transaction_id,' . $payment->id,
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,completed,failed,refunded',
            'payment_details' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $payment->update([
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'amount' => $request->amount,
            'status' => $request->status,
            'payment_details' => $request->payment_details,
            'paid_at' => $request->status == 'completed' && !$payment->paid_at ? now() : $payment->paid_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully',
            'redirect' => route('payments.index')
        ]);
    }

    /**
     * Remove the specified payment (DELETE)
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ]);
    }
}