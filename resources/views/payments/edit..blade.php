@extends('layouts.app')

@section('title', 'Edit Payment')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-edit"></i> Edit Payment</h1>
        <a href="{{ route('payments.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="form-container">
        <form id="paymentForm" action="{{ route('payments.update', $payment->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Order</label>
                <input 
                    type="text" 
                    class="form-control" 
                    value="{{ $payment->order->order_number }} - {{ $payment->order->user->name }}"
                    readonly
                >
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="payment_method"><i class="fas fa-credit-card"></i> Payment Method *</label>
                    <select class="form-control" id="payment_method" name="payment_method" required>
                        <option value="cash" {{ $payment->payment_method == 'cash' ? 'selected' : '' }}>💵 Cash</option>
                        <option value="mpesa" {{ $payment->payment_method == 'mpesa' ? 'selected' : '' }}>📱 M-Pesa</option>
                        <option value="card" {{ $payment->payment_method == 'card' ? 'selected' : '' }}>💳 Card</option>
                        <option value="bank_transfer" {{ $payment->payment_method == 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="amount"><i class="fas fa-money-bill"></i> Amount (TZS) *</label>
                    <input 
                        type="number" 
                        class="form-control" 
                        id="amount" 
                        name="amount" 
                        value="{{ $payment->amount }}"
                        step="0.01"
                        required
                    >
                </div>
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="transaction_id"><i class="fas fa-hashtag"></i> Transaction ID</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="transaction_id" 
                        name="transaction_id" 
                        value="{{ $payment->transaction_id }}"
                    >
                </div>
                
                <div class="form-group">
                    <label for="status"><i class="fas fa-check-circle"></i> Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ $payment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ $payment->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="payment_details"><i class="fas fa-info-circle"></i> Payment Details</label>
                <textarea 
                    class="form-control" 
                    id="payment_details" 
                    name="payment_details" 
                    rows="3"
                >{{ $payment->payment_details }}</textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Payment
                </button>
                <a href="{{ route('payments.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="button" class="btn btn-danger" onclick="deletePayment({{ $payment->id }})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
handleFormSubmit('paymentForm');

async function deletePayment(paymentId) {
    if (!confirmDelete('Are you sure you want to delete this payment?')) return;
    
    try {
        const response = await fetchAPI(`/payments/${paymentId}`, {
            method: 'DELETE'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => {
                window.location.href = '{{ route("payments.index") }}';
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete payment', 'error');
    }
}
</script>
@endpush