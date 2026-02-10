@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-plus"></i> Record New Payment</h1>
        <a href="{{ route('payments.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="form-container">
        <form id="paymentForm" action="{{ route('payments.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="order_id"><i class="fas fa-shopping-cart"></i> Order *</label>
                <select class="form-control" id="order_id" name="order_id" required>
                    <option value="">Select Order</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}">
                            {{ $order->order_number }} - {{ $order->user->name }} (TZS {{ number_format($order->total_amount, 0) }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="payment_method"><i class="fas fa-credit-card"></i> Payment Method *</label>
                    <select class="form-control" id="payment_method" name="payment_method" required>
                        <option value="">Select Method</option>
                        <option value="cash">💵 Cash</option>
                        <option value="mpesa">📱 M-Pesa</option>
                        <option value="card">💳 Card</option>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="amount"><i class="fas fa-money-bill"></i> Amount (TZS) *</label>
                    <input 
                        type="number" 
                        class="form-control" 
                        id="amount" 
                        name="amount" 
                        placeholder="0"
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
                        placeholder="e.g., MPESA123456"
                    >
                </div>
                
                <div class="form-group">
                    <label for="status"><i class="fas fa-check-circle"></i> Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="completed" selected>Completed</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="payment_details"><i class="fas fa-info-circle"></i> Payment Details (Optional)</label>
                <textarea 
                    class="form-control" 
                    id="payment_details" 
                    name="payment_details" 
                    rows="3"
                    placeholder="Additional payment information..."
                ></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Record Payment
                </button>
                <a href="{{ route('payments.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
handleFormSubmit('paymentForm');

// Auto-fill amount when order is selected
document.getElementById('order_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        const text = selectedOption.text;
        const amountMatch = text.match(/TZS ([\d,]+)/);
        if (amountMatch) {
            const amount = amountMatch[1].replace(/,/g, '');
            document.getElementById('amount').value = amount;
        }
    }
});
</script>
@endpush