@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Order Details</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline">
            ← Back to Orders
        </a>
    </div>
    
    <div class="order-details-container">
        <!-- Order Info -->
        <div class="order-info-card">
            <div class="card">
                <div class="card-header">
                    <h2>Order Information</h2>
                    <div class="order-status-large">
                        @if($order->status == 'pending')
                            <span class="badge badge-warning">⏳ Pending</span>
                        @elseif($order->status == 'processing')
                            <span class="badge badge-primary">🔄 Processing</span>
                        @elseif($order->status == 'completed')
                            <span class="badge badge-success">✅ Completed</span>
                        @else
                            <span class="badge badge-danger">❌ Cancelled</span>
                        @endif
                    </div>
                </div>
                
                <div class="order-info-grid">
                    <div class="info-item">
                        <label>Order Number</label>
                        <strong>{{ $order->order_number }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Order Date</label>
                        <strong>{{ $order->created_at->format('M d, Y H:i') }}</strong>
                    </div>
                    
                    @if(auth()->user()->isAdmin())
                    <div class="info-item">
                        <label>Customer</label>
                        <strong>{{ $order->user->name }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Customer Email</label>
                        <strong>{{ $order->user->email }}</strong>
                    </div>
                    @endif
                    
                    <div class="info-item">
                        <label>Phone Number</label>
                        <strong>{{ $order->phone }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Total Amount</label>
                        <strong class="text-primary">TZS {{ number_format($order->total_amount, 0) }}</strong>
                    </div>
                </div>
                
                <div class="delivery-info">
                    <h3>Delivery Address</h3>
                    <p>{{ $order->delivery_address }}</p>
                </div>
                
                @if($order->notes)
                <div class="order-notes">
                    <h3>Order Notes</h3>
                    <p>{{ $order->notes }}</p>
                </div>
                @endif
                
                <!-- Admin Actions -->
                @if(auth()->user()->isAdmin())
                <div class="admin-actions-section">
                    <h3>Admin Actions</h3>
                    <div class="status-update-form">
                        <select id="orderStatus" class="form-control">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button class="btn btn-primary" onclick="updateOrderStatus({{ $order->id }})">
                            Update Status
                        </button>
                    </div>
                </div>
                @endif
                
                <!-- Customer Actions -->
                @if(auth()->user()->isCustomer() && $order->status == 'pending')
                <div class="customer-actions">
                    <button class="btn btn-danger" onclick="cancelOrder({{ $order->id }})">
                        Cancel Order
                    </button>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="order-items-card">
            <div class="card">
                <div class="card-header">
                    <h2>Order Items</h2>
                </div>
                
                <div class="order-items-list">
                    @foreach($order->orderItems as $item)
                    <div class="order-item-detail">
                        <div class="item-image">
                            @if($item->product->image)
                                <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}">
                            @else
                                <img src="https://via.placeholder.com/80?text=Food" alt="{{ $item->product->name }}">
                            @endif
                        </div>
                        
                        <div class="item-info">
                            <h4>{{ $item->product->name }}</h4>
                            <p class="item-category">{{ $item->product->category->name }}</p>
                            <p class="item-price">TZS {{ number_format($item->price, 0) }} × {{ $item->quantity }}</p>
                        </div>
                        
                        <div class="item-subtotal">
                            <strong>TZS {{ number_format($item->subtotal, 0) }}</strong>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="order-summary-total">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>TZS {{ number_format($order->total_amount, 0) }}</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Delivery Fee</span>
                        <span>TZS 0</span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span>TZS {{ number_format($order->total_amount, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .order-details-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .order-status-large .badge {
        font-size: 1rem;
        padding: 8px 16px;
    }
    
    .order-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin: 1.5rem 0;
    }
    
    .info-item label {
        display: block;
        font-size: 0.875rem;
        color: #636e72;
        margin-bottom: 0.25rem;
    }
    
    .info-item strong {
        font-size: 1.1rem;
        color: var(--dark-color);
    }
    
    .text-primary {
        color: var(--primary-color) !important;
    }
    
    .delivery-info,
    .order-notes {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid var(--border-color);
    }
    
    .delivery-info h3,
    .order-notes h3 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .delivery-info p,
    .order-notes p {
        color: #636e72;
    }
    
    .admin-actions-section,
    .customer-actions {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid var(--border-color);
    }
    
    .admin-actions-section h3 {
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    
    .status-update-form {
        display: flex;
        gap: 1rem;
    }
    
    .status-update-form select {
        flex: 1;
    }
    
    .order-items-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .order-item-detail {
        display: grid;
        grid-template-columns: 80px 1fr auto;
        gap: 1rem;
        padding: 1rem;
        background: var(--light-color);
        border-radius: 8px;
    }
    
    .item-image img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .item-info h4 {
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
    }
    
    .item-category {
        color: var(--primary-color);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .item-price {
        color: #636e72;
        font-size: 0.875rem;
    }
    
    .item-subtotal {
        text-align: right;
        font-size: 1.25rem;
        color: var(--primary-color);
    }
    
    .order-summary-total {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid var(--border-color);
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .summary-divider {
        border-top: 2px solid var(--border-color);
        margin: 1rem 0;
    }
    
    .summary-total {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    
    @media (max-width: 768px) {
        .order-details-container {
            grid-template-columns: 1fr;
        }
        
        .order-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
async function updateOrderStatus(orderId) {
    const status = document.getElementById('orderStatus').value;
    
    if (!confirm(`Update order status to "${status}"?`)) return;
    
    try {
        const response = await fetchAPI(`/orders/${orderId}/update-status`, {
            method: 'POST',
            body: JSON.stringify({ status: status })
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to update status', 'error');
    }
}

async function cancelOrder(orderId) {
    if (!confirm('Are you sure you want to cancel this order?')) return;
    
    try {
        const response = await fetchAPI(`/orders/${orderId}/cancel`, {
            method: 'POST'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to cancel order', 'error');
    }
}
</script>
@endpush