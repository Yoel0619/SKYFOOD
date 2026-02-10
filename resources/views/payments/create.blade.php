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
    
    <div class="card">
        <div class="card-header">
            <h2>Select Order to Record Payment</h2>
            <p style="margin: 0; color: #757575; font-size: 0.9rem;">Click on an order to record its payment</p>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->user->name }}</td>
                        <td>
                            @foreach($order->orderItems as $item)
                                <span class="badge badge-primary">{{ $item->quantity }}x {{ $item->product->name }}</span>
                            @endforeach
                        </td>
                        <td><strong style="color: #fbaf32;">TZS {{ number_format($order->total_amount, 0) }}</strong></td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($order->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($order->status == 'processing')
                                <span class="badge badge-primary">Processing</span>
                            @elseif($order->status == 'completed')
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="openPaymentModal({{ $order->id }}, '{{ $order->order_number }}', {{ $order->total_amount }})">
                                <i class="fas fa-money-bill"></i> Record Payment
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            <p>No orders available for payment</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                Browse Products & Create Order
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-money-bill-wave"></i> Record Payment</h2>
            <button class="close-modal" onclick="closePaymentModal()">&times;</button>
        </div>
        
        <form id="paymentForm" action="{{ route('payments.store') }}" method="POST">
            @csrf
            
            <input type="hidden" id="order_id" name="order_id">
            
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Order:</strong> <span id="modal_order_number"></span><br>
                    <strong>Amount:</strong> TZS <span id="modal_amount"></span>
                </div>
                
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
                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" readonly required>
                </div>
                
                <div class="form-group">
                    <label for="transaction_id"><i class="fas fa-hashtag"></i> Transaction ID</label>
                    <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="e.g., MPESA123456">
                </div>
                
                <div class="form-group">
                    <label for="status"><i class="fas fa-check-circle"></i> Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="completed" selected>✅ Completed</option>
                        <option value="pending">⏳ Pending</option>
                        <option value="failed">❌ Failed</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="payment_details"><i class="fas fa-info-circle"></i> Payment Details (Optional)</label>
                    <textarea class="form-control" id="payment_details" name="payment_details" rows="3" placeholder="Additional information..."></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closePaymentModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Record Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal-content {
    background: white;
    border-radius: 15px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
}

.close-modal {
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    color: #757575;
    transition: all 0.3s;
}

.close-modal:hover {
    color: #d63031;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 2px solid #f0f0f0;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-info {
    background: rgba(52, 152, 219, 0.1);
    border-left: 4px solid #3498db;
    color: #2c3e50;
}
</style>
@endpush

@push('scripts')
<script>
function openPaymentModal(orderId, orderNumber, amount) {
    document.getElementById('order_id').value = orderId;
    document.getElementById('modal_order_number').textContent = orderNumber;
    document.getElementById('modal_amount').textContent = amount.toLocaleString();
    document.getElementById('amount').value = amount;
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
    document.getElementById('paymentForm').reset();
}

handleFormSubmit('paymentForm', function(response) {
    if (response.success) {
        closePaymentModal();
    }
});
</script>
@endpush