@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>SKYFOOD🌃💫</h1>
        <p>Welcome back, {{ auth()->user()->name }}!</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e3f2fd;">
                <span>📦</span>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_orders'] }}</h3>
                <p>Total Orders</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff3e0;">
                <span>⏳</span>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['pending_orders'] }}</h3>
                <p>Pending Orders</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #e8f5e9;">
                <span>✅</span>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['completed_orders'] }}</h3>
                <p>Completed Orders</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #f3e5f5;">
                <span>💰</span>
            </div>
            <div class="stat-content">
                <h3>TZS {{ number_format($stats['total_spent'], 0) }}</h3>
                <p>Total Spent</p>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2>Quick Actions</h2>
        </div>
        <div class="quick-actions">
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                🍔 Browse Menu
            </a>
            <a href="{{ route('cart.index') }}" class="btn btn-secondary">
                🛒 View Cart
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-success">
                📋 My Orders
            </a>
            <a href="{{ route('profile') }}" class="btn btn-warning">
                👤 My Profile
            </a>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h2>Recent Orders</h2>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->orderItems->count() }} items</td>
                        <td>TZS {{ number_format($order->total_amount, 0) }}</td>
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
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                View
                            </a>
                            @if($order->status == 'pending')
                            <button 
                                class="btn btn-sm btn-danger" 
                                onclick="cancelOrder({{ $order->id }})"
                            >
                                Cancel
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">
                            <p>No orders yet</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary" style="margin-top: 1rem;">
                                Start Ordering
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .page-header {
        margin-bottom: 2rem;
    }
    
    .page-header h1 {
        font-size: 2rem;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }
    
    .page-header p {
        color: #636e72;
    }
    
    .quick-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endpush

@push('scripts')
<script>
async function cancelOrder(orderId) {
    if (!confirm('Are you sure you want to cancel this order?')) {
        return;
    }
    
    try {
        const response = await fetchAPI(`/orders/${orderId}/cancel`, {
            method: 'POST'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to cancel order', 'error');
    }
}
</script>
@endpush