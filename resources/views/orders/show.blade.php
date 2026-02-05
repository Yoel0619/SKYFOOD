@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.my') }}">My Orders</a></li>
            <li class="breadcrumb-item active">Order #{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Order Number:</strong></p>
                            <p class="text-muted">{{ $order->order_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Order Date:</strong></p>
                            <p class="text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Order Status:</strong></p>
                            <span class="badge 
                                @if($order->order_status == 'delivered') bg-success
                                @elseif($order->order_status == 'cancelled') bg-danger
                                @elseif($order->order_status == 'out_for_delivery') bg-info
                                @else bg-warning
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Payment Status:</strong></p>
                            <span class="badge 
                                @if($order->payment_status == 'paid') bg-success
                                @elseif($order->payment_status == 'failed') bg-danger
                                @else bg-warning
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Payment Method:</strong></p>
                            <p class="text-muted">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        </div>
                        @if($order->estimated_delivery_time)
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Estimated Delivery:</strong></p>
                                <p class="text-muted">{{ $order->estimated_delivery_time->format('h:i A') }}</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($order->delivered_at)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Delivered on {{ $order->delivered_at->format('d M Y, h:i A') }}
                        </div>
                    @endif
                    
                    @if($order->cancelled_at)
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> Cancelled on {{ $order->cancelled_at->format('d M Y, h:i A') }}
                            @if($order->cancellation_reason)
                                <br><strong>Reason:</strong> {{ $order->cancellation_reason }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-utensils"></i> Order Items</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->food->image)
                                                <img src="{{ asset('storage/' . $item->food->image) }}" alt="{{ $item->food->name }}" class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                {{ $item->food->name }}
                                                @if($item->special_instructions)
                                                    <br><small class="text-muted">Note: {{ $item->special_instructions }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>TSh {{ number_format($item->unit_price, 0) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>TSh {{ number_format($item->subtotal, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Delivery Address</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $order->deliveryAddress->label }}</strong></p>
                    <p class="text-muted mb-0">{{ $order->deliveryAddress->full_address }}</p>
                </div>
            </div>

            @if($order->special_instructions)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-comment"></i> Special Instructions</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->special_instructions }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>TSh {{ number_format($order->subtotal, 0) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount:</span>
                            <span>-TSh {{ number_format($order->discount_amount, 0) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax (16%):</span>
                        <span>TSh {{ number_format($order->tax_amount, 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee:</span>
                        <span>TSh {{ number_format($order->delivery_fee, 0) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong class="text-primary">TSh {{ number_format($order->total_amount, 0) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Order Status Timeline -->
            @if($order->statusHistory->count() > 0)
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Order Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($order->statusHistory->sortByDesc('changed_at') as $history)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-marker">
                                            <i class="fas fa-circle text-primary"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="mb-1"><strong>{{ ucfirst(str_replace('_', ' ', $history->new_status)) }}</strong></p>
                                            <p class="small text-muted mb-1">{{ $history->changed_at->format('d M Y, h:i A') }}</p>
                                            @if($history->remarks)
                                                <p class="small mb-0">{{ $history->remarks }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($order->canBeCancelled())
                <div class="d-grid gap-2 mt-3">
                    <button class="btn btn-danger cancel-order" data-order-id="{{ $order->id }}">
                        <i class="fas fa-times"></i> Cancel Order
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="cancelOrderForm">
                    @csrf
                    <input type="hidden" id="cancel_order_id" name="order_id">
                    <div class="mb-3">
                        <label class="form-label">Reason for Cancellation</label>
                        <textarea class="form-control" name="reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn">Cancel Order</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/orders.js') }}"></script>
@endpush