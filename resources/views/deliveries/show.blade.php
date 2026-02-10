@extends('layouts.app')

@section('title', 'Delivery Details')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-truck"></i> Delivery Details</h1>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('deliveries.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('deliveries.edit', $delivery->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
    
    <div class="delivery-detail-container">
        <!-- Delivery Info -->
        <div class="delivery-info-card">
            <div class="card">
                <div class="card-header">
                    <h2>Delivery Information</h2>
                    @switch($delivery->status)
                        @case('pending')
                            <span class="badge badge-warning">⏳ Pending</span>
                            @break
                        @case('assigned')
                            <span class="badge" style="background: #3498db;">👤 Assigned</span>
                            @break
                        @case('picked_up')
                            <span class="badge" style="background: #9b59b6;">📦 Picked Up</span>
                            @break
                        @case('in_transit')
                            <span class="badge" style="background: #f39c12;">🚚 In Transit</span>
                            @break
                        @case('delivered')
                            <span class="badge badge-success">✅ Delivered</span>
                            @break
                        @case('cancelled')
                            <span class="badge badge-danger">❌ Cancelled</span>
                            @break
                    @endswitch
                </div>
                
                <div class="delivery-info-grid">
                    <div class="info-item">
                        <label>Tracking Number</label>
                        <strong style="color: #fbaf32; font-size: 1.3rem;">{{ $delivery->tracking_number }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Order Number</label>
                        <strong>{{ $delivery->order->order_number }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Customer</label>
                        <strong>{{ $delivery->order->user->name }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Delivery Person</label>
                        <strong>{{ $delivery->deliveryPerson->name ?? 'Not Assigned' }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Phone Number</label>
                        <strong>{{ $delivery->phone }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Delivery Fee</label>
                        <strong style="color: #fbaf32;">TZS {{ number_format($delivery->delivery_fee, 0) }}</strong>
                    </div>
                </div>
                
                <div class="delivery-address-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Delivery Address</h3>
                    <p>{{ $delivery->delivery_address }}</p>
                </div>
                
                @if($delivery->delivery_notes)
                <div class="delivery-notes-section">
                    <h3><i class="fas fa-sticky-note"></i> Delivery Notes</h3>
                    <p>{{ $delivery->delivery_notes }}</p>
                </div>
                @endif
            </div>
            
            <!-- Delivery Timeline -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-history"></i> Delivery Timeline</h2>
                </div>
                
                <div class="delivery-timeline">
                    <div class="timeline-step {{ $delivery->created_at ? 'completed' : '' }}">
                        <div class="step-icon">1</div>
                        <div class="step-content">
                            <h4>Delivery Created</h4>
                            <p>{{ $delivery->created_at ? $delivery->created_at->format('M d, Y H:i') : 'Not yet' }}</p>
                        </div>
                    </div>
                    
                    <div class="timeline-step {{ $delivery->assigned_at ? 'completed' : '' }}">
                        <div class="step-icon">2</div>
                        <div class="step-content">
                            <h4>Assigned to Delivery Person</h4>
                            <p>{{ $delivery->assigned_at ? $delivery->assigned_at->format('M d, Y H:i') : 'Not yet' }}</p>
                        </div>
                    </div>
                    
                    <div class="timeline-step {{ $delivery->picked_up_at ? 'completed' : '' }}">
                        <div class="step-icon">3</div>
                        <div class="step-content">
                            <h4>Order Picked Up</h4>
                            <p>{{ $delivery->picked_up_at ? $delivery->picked_up_at->format('M d, Y H:i') : 'Not yet' }}</p>
                        </div>
                    </div>
                    
                    <div class="timeline-step {{ $delivery->status == 'in_transit' ? 'active' : '' }} {{ $delivery->delivered_at ? 'completed' : '' }}">
                        <div class="step-icon">4</div>
                        <div class="step-content">
                            <h4>In Transit</h4>
                            <p>{{ $delivery->status == 'in_transit' ? 'Currently in transit' : 'Not yet' }}</p>
                        </div>
                    </div>
                    
                    <div class="timeline-step {{ $delivery->delivered_at ? 'completed' : '' }}">
                        <div class="step-icon">5</div>
                        <div class="step-content">
                            <h4>Delivered</h4>
                            <p>{{ $delivery->delivered_at ? $delivery->delivered_at->format('M d, Y H:i') : 'Not yet' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Details -->
        <div class="order-info-card">
            <div class="card">
                <div class="card-header">
                    <h2>Order Information</h2>
                    <a href="{{ route('orders.show', $delivery->order->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View Full Order
                    </a>
                </div>
                
                <div class="delivery-info-grid">
                    <div class="info-item">
                        <label>Order Number</label>
                        <strong>{{ $delivery->order->order_number }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Order Total</label>
                        <strong>TZS {{ number_format($delivery->order->total_amount, 0) }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Order Status</label>
                        <strong>{{ ucfirst($delivery->order->status) }}</strong>
                    </div>
                    
                    <div class="info-item">
                        <label>Items</label>
                        <strong>{{ $delivery->order->orderItems->count() }} items</strong>
                    </div>
                </div>
                
                <div class="order-items-list">
                    <h3>Order Items</h3>
                    @foreach($delivery->order->orderItems as $item)
                    <div class="order-item-mini">
                        <div class="item-image-mini">
                            @if($item->product->image)
                                <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}">
                            @else
                                <div class="placeholder-img">🍴</div>
                            @endif
                        </div>
                        <div class="item-details-mini">
                            <h4>{{ $item->product->name }}</h4>
                            <p>Qty: {{ $item->quantity }} × TZS {{ number_format($item->price, 0) }}</p>
                        </div>
                        <div class="item-total-mini">
                            TZS {{ number_format($item->subtotal, 0) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .delivery-detail-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .delivery-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        padding: 1.5rem;
    }
    
    .info-item label {
        display: block;
        font-size: 0.875rem;
        color: #757575;
        margin-bottom: 0.25rem;
    }
    
    .info-item strong {
        font-size: 1.1rem;
        color: #454545;
    }
    
    .delivery-address-section,
    .delivery-notes-section {
        margin-top: 1.5rem;
        padding: 1.5rem;
        border-top: 2px solid #f8f9fa;
    }
    
    .delivery-address-section h3,
    .delivery-notes-section h3 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        color: #454545;
    }
    
    .delivery-address-section p,
    .delivery-notes-section p {
        color: #757575;
    }
    
    /* Delivery Timeline */
    .delivery-timeline {
        padding: 2rem;
    }
    
    .timeline-step {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 2rem;
        position: relative;
        padding-left: 40px;
    }
    
    .timeline-step:not(:last-child)::before {
        content: "";
        position: absolute;
        left: 20px;
        top: 40px;
        bottom: -20px;
        width: 3px;
        background: #e0e0e0;
    }
    
    .timeline-step.completed::before {
        background: linear-gradient(180deg, #fbaf32, #719a0a);
    }
    
    .step-icon {
        position: absolute;
        left: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        z-index: 1;
    }
    
    .timeline-step.completed .step-icon {
        background: linear-gradient(135deg, #fbaf32, #719a0a);
        color: white;
    }
    
    .timeline-step.active .step-icon {
        background: #3498db;
        color: white;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(52, 152, 219, 0);
        }
    }
    
    .step-content h4 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: #454545;
    }
    
    .step-content p {
        font-size: 0.875rem;
        color: #757575;
        margin: 0;
    }
    
    .timeline-step.completed .step-content h4 {
        color: #fbaf32;
    }
    
    /* Order Items List */
    .order-items-list {
        margin-top: 1.5rem;
        padding: 1.5rem;
        border-top: 2px solid #f8f9fa;
    }
    
    .order-items-list h3 {
        font-size: 1rem;
        margin-bottom: 1rem;
        color: #454545;
    }
    
    .order-item-mini {
        display: grid;
        grid-template-columns: 50px 1fr auto;
        gap: 1rem;
        padding: 1rem;
        background: rgba(251, 175, 50, 0.05);
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    
    .item-image-mini img,
    .placeholder-img {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
    }
    
    .placeholder-img {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        font-size: 24px;
    }
    
    .item-details-mini h4 {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .item-details-mini p {
        font-size: 0.8rem;
        color: #757575;
        margin: 0;
    }
    
    .item-total-mini {
        font-weight: 700;
        color: #fbaf32;
        text-align: right;
    }
    
    @media (max-width: 768px) {
        .delivery-detail-container {
            grid-template-columns: 1fr;
        }
        
        .delivery-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush