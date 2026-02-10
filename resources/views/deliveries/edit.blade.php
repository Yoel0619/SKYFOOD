@extends('layouts.app')

@section('title', 'Edit Delivery')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-edit"></i> Edit Delivery</h1>
        <a href="{{ route('deliveries.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="form-container">
        <form id="deliveryForm" action="{{ route('deliveries.update', $delivery->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> Order:</strong> 
                {{ $delivery->order->order_number }} - {{ $delivery->order->user->name }}
            </div>
            
            <div class="alert alert-warning">
                <strong><i class="fas fa-barcode"></i> Tracking Number:</strong> 
                {{ $delivery->tracking_number }}
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="delivery_person_id"><i class="fas fa-user"></i> Delivery Person</label>
                    <select class="form-control" id="delivery_person_id" name="delivery_person_id">
                        <option value="">Not Assigned</option>
                        @foreach($deliveryPersons as $person)
                            <option value="{{ $person->id }}" {{ $delivery->delivery_person_id == $person->id ? 'selected' : '' }}>
                                {{ $person->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="delivery_fee"><i class="fas fa-money-bill"></i> Delivery Fee (TZS) *</label>
                    <input 
                        type="number" 
                        class="form-control" 
                        id="delivery_fee" 
                        name="delivery_fee" 
                        value="{{ $delivery->delivery_fee }}"
                        step="0.01"
                        required
                    >
                </div>
            </div>
            
            <div class="form-group">
                <label for="delivery_address"><i class="fas fa-map-marker-alt"></i> Delivery Address *</label>
                <textarea 
                    class="form-control" 
                    id="delivery_address" 
                    name="delivery_address" 
                    rows="3"
                    required
                >{{ $delivery->delivery_address }}</textarea>
            </div>
            
            <div class="form-group">
                <label for="phone"><i class="fas fa-phone"></i> Phone Number *</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="phone" 
                    name="phone" 
                    value="{{ $delivery->phone }}"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="status"><i class="fas fa-check-circle"></i> Status *</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="pending" {{ $delivery->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="assigned" {{ $delivery->status == 'assigned' ? 'selected' : '' }}>👤 Assigned</option>
                    <option value="picked_up" {{ $delivery->status == 'picked_up' ? 'selected' : '' }}>📦 Picked Up</option>
                    <option value="in_transit" {{ $delivery->status == 'in_transit' ? 'selected' : '' }}>🚚 In Transit</option>
                    <option value="delivered" {{ $delivery->status == 'delivered' ? 'selected' : '' }}>✅ Delivered</option>
                    <option value="cancelled" {{ $delivery->status == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="delivery_notes"><i class="fas fa-sticky-note"></i> Delivery Notes</label>
                <textarea 
                    class="form-control" 
                    id="delivery_notes" 
                    name="delivery_notes" 
                    rows="3"
                >{{ $delivery->delivery_notes }}</textarea>
            </div>
            
            <!-- Delivery Timeline Info -->
            <div class="delivery-timeline-info">
                <h3><i class="fas fa-clock"></i> Delivery Timeline</h3>
                <div class="timeline-grid">
                    <div class="timeline-item">
                        <label>Assigned:</label>
                        <span>{{ $delivery->assigned_at ? $delivery->assigned_at->format('M d, Y H:i') : 'Not yet' }}</span>
                    </div>
                    <div class="timeline-item">
                        <label>Picked Up:</label>
                        <span>{{ $delivery->picked_up_at ? $delivery->picked_up_at->format('M d, Y H:i') : 'Not yet' }}</span>
                    </div>
                    <div class="timeline-item">
                        <label>Delivered:</label>
                        <span>{{ $delivery->delivered_at ? $delivery->delivered_at->format('M d, Y H:i') : 'Not yet' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Delivery
                </button>
                <a href="{{ route('deliveries.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="button" class="btn btn-danger" onclick="deleteDelivery({{ $delivery->id }})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .alert-info {
        background: rgba(52, 152, 219, 0.1);
        border-left: 4px solid #3498db;
        color: #2c3e50;
    }
    
    .alert-warning {
        background: rgba(243, 156, 18, 0.1);
        border-left: 4px solid #f39c12;
        color: #2c3e50;
    }
    
    .delivery-timeline-info {
        margin: 2rem 0;
        padding: 1.5rem;
        background: rgba(251, 175, 50, 0.05);
        border-radius: 10px;
        border-left: 4px solid #fbaf32;
    }
    
    .delivery-timeline-info h3 {
        font-size: 1.2rem;
        margin-bottom: 1rem;
        color: #454545;
    }
    
    .timeline-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    
    .timeline-item {
        background: white;
        padding: 1rem;
        border-radius: 8px;
    }
    
    .timeline-item label {
        display: block;
        font-weight: 600;
        color: #757575;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .timeline-item span {
        color: #454545;
        font-weight: 700;
    }
    
    @media (max-width: 768px) {
        .timeline-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
handleFormSubmit('deliveryForm');

async function deleteDelivery(deliveryId) {
    if (!confirmDelete('Are you sure you want to delete this delivery?')) return;
    
    try {
        const response = await fetchAPI(`/deliveries/${deliveryId}`, {
            method: 'DELETE'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => {
                window.location.href = '{{ route("deliveries.index") }}';
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete delivery', 'error');
    }
}
</script>
@endpush