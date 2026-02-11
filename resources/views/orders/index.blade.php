@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>
            @if(auth()->user()->isAdmin())
                All Orders
            @else
                My Orders
            @endif
        </h1>
    </div>
    
    <!-- Search & Filter -->
    <div class="filter-bar">
        <input 
            type="text" 
            class="form-control" 
            id="searchInput" 
            placeholder="🔍 Search orders..."
            value="{{ request('search') }}"
        >
        
        <select class="form-control" id="statusFilter">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        
        <button class="btn btn-secondary" onclick="applyFilters()">
            Filter
        </button>
        
        <button class="btn btn-outline" onclick="clearFilters()">
            Clear
        </button>
    </div>
    
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        @if(auth()->user()->isAdmin())
                            <th>Customer</th>
                        @endif
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    @forelse($orders as $order)
    <tr>
        <td><strong>{{ $order->order_number }}</strong></td>
        <td>{{ $order->user->name }}</td>
        <td>
            <strong>{{ $order->orderItems->count() }} items</strong>
            <div style="font-size: 0.85rem; color: #757575; margin-top: 0.25rem;">
                @foreach($order->orderItems->take(2) as $item)
                    <span class="badge badge-primary" style="margin: 0.25rem;">
                        {{ $item->quantity }}x {{ $item->product->name }}
                    </span>
                @endforeach
                @if($order->orderItems->count() > 2)
                    <span class="badge" style="background: #95a5a6;">+{{ $order->orderItems->count() - 2 }} more</span>
                @endif
            </div>
        </td>
        <td><strong style="color: #fbaf32;">TZS {{ number_format($order->total_amount, 0) }}</strong></td>
        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
        <td>
            @if($order->status == 'pending')
                <span class="badge badge-warning">⏳ Pending</span>
            @elseif($order->status == 'processing')
                <span class="badge badge-primary">🔄 Processing</span>
            @elseif($order->status == 'completed')
                <span class="badge badge-success">✅ Completed</span>
            @else
                <span class="badge badge-danger">❌ Cancelled</span>
            @endif
        </td>
        <td>
            <div class="action-buttons">
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> View
                </a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button 
                        class="btn btn-sm btn-danger" 
                        onclick="deleteItem('/orders/{{ $order->id }}', 'Delete order {{ $order->order_number }}?')"
                    >
                        <i class="fas fa-trash"></i> Delete
                    </button>
                @endif
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" style="text-align: center; padding: 3rem;">
            <div style="font-size: 60px; margin-bottom: 1rem;">📦</div>
            <h3>No Orders Yet</h3>
            <p style="color: #757575; margin-bottom: 1.5rem;">Start shopping to create your first order!</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Browse Products
            </a>
        </td>
    </tr>
    @endforelse
</tbody>
            </table>
        </div>
        
        <div class="pagination-container">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("orders.index") }}?' + params.toString();
}

function clearFilters() {
    window.location.href = '{{ route("orders.index") }}';
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

async function updateStatus(orderId) {
    const status = prompt('Enter new status (pending, processing, completed, cancelled):');
    
    if (!status) return;
    
    const validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!validStatuses.includes(status.toLowerCase())) {
        alert('Invalid status!');
        return;
    }
    
    try {
        const response = await fetchAPI(`/orders/${orderId}/update-status`, {
            method: 'POST',
            body: JSON.stringify({ status: status.toLowerCase() })
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to update status', 'error');
    }
}
</script>
@endpush