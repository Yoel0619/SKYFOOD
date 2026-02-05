@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container">
    <h1 class="mb-4">My Orders</h1>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('orders.my') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="">All Orders</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($orders->count() > 0)
        @foreach($orders as $order)
        <div class="card mb-3">
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <strong>Order #{{ $order->order_number }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</small>
                    </div>
                    <div class="col-md-3">
                        <span class="badge 
                            @if($order->order_status == 'pending') bg-warning
                            @elseif($order->order_status == 'confirmed') bg-info
                            @elseif($order->order_status == 'preparing') bg-primary
                            @elseif($order->order_status == 'ready') bg-success
                            @elseif($order->order_status == 'out_for_delivery') bg-primary
                            @elseif($order->order_status == 'delivered') bg-success
                            @elseif($order->order_status == 'cancelled') bg-danger
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                        </span>
                    </div>
                    <div class="col-md-3 text-end">
                        <strong class="text-primary">TSh {{ number_format($order->total_amount, 0) }}</strong>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6>Items:</h6>
                        <ul class="list-unstyled">
                            @foreach($order->items as $item)
                            <li class="mb-1">
                                <span class="badge bg-secondary">{{ $item->quantity }}x</span>
                                {{ $item->food->name }}
                                <span class="text-muted">- TSh {{ number_format($item->subtotal, 0) }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        @if($order->canBeCancelled())
                        <button class="btn btn-outline-danger btn-sm cancel-order" data-order-id="{{ $order->id }}">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-receipt fa-5x text-muted mb-3"></i>
            <h3>No orders found</h3>
            <p class="text-muted">Start ordering delicious food!</p>
            <a href="{{ route('menu.index') }}" class="btn btn-primary">
                <i class="fas fa-book-open"></i> Browse Menu
            </a>
        </div>
    @endif
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cancelOrderForm">
                @csrf
                <input type="hidden" name="order_id" id="cancelOrderId">
                <div class="modal-body">
                    <p>Are you sure you want to cancel this order?</p>
                    <div class="mb-3">
                        <label class="form-label">Reason for cancellation</label>
                        <textarea class="form-control" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/orders.js') }}"></script>
@endpush