@extends('layouts.app')

@section('title', 'Create Delivery')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-truck"></i> Create New Delivery</h1>
        <a href="{{ route('deliveries.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="form-container">
        <div class="card">
            <form id="deliveryForm" action="{{ route('deliveries.store') }}" method="POST">
                @csrf
                
                <!-- Order Selection with Better Display -->
                <div class="form-group">
                    <label for="order_id"><i class="fas fa-shopping-cart"></i> Select Order *</label>
                    <select class="form-control select-order" id="order_id" name="order_id" required>
                        <option value="">-- Choose Order to Deliver --</option>
                        @forelse($orders as $order)
                            <option value="{{ $order->id }}" 
                                    data-address="{{ $order->delivery_address }}"
                                    data-phone="{{ $order->phone }}"
                                    data-customer="{{ $order->user->name }}"
                                    data-amount="{{ $order->total_amount }}">
                                {{ $order->order_number }} - {{ $order->user->name }} - TZS {{ number_format($order->total_amount, 0) }}
                            </option>
                        @empty
                            <option value="" disabled>No orders pending delivery</option>
                        @endforelse
                    </select>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Select an order that needs to be delivered to the customer
                    </small>
                </div>
                
                <!-- Order Details Preview -->
                <div id="orderPreview" class="order-preview" style="display: none;">
                    <h4><i class="fas fa-receipt"></i> Order Details</h4>
                    <div class="preview-grid">
                        <div class="preview-item">
                            <label>Customer:</label>
                            <span id="previewCustomer">-</span>
                        </div>
                        <div class="preview-item">
                            <label>Amount:</label>
                            <span id="previewAmount">-</span>
                        </div>
                        <div class="preview-item">
                            <label>Address:</label>
                            <span id="previewAddress">-</span>
                        </div>
                        <div class="preview-item">
                            <label>Phone:</label>
                            <span id="previewPhone">-</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="delivery_person_id"><i class="fas fa-user"></i> Delivery Person</label>
                        <select class="form-control" id="delivery_person_id" name="delivery_person_id">
                            <option value="">Not Assigned Yet</option>
                            @foreach($deliveryPersons as $person)
                                <option value="{{ $person->id }}">
                                    {{ $person->name }} 
                                    @if($person->phone)
                                    - {{ $person->phone }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> You can assign later
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="delivery_fee"><i class="fas fa-money-bill-wave"></i> Delivery Fee (TZS) *</label>
                        <input type="number" class="form-control" id="delivery_fee" name="delivery_fee" 
                               value="0" step="1000" min="0" required>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Enter 0 if no delivery fee
                        </small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="delivery_address"><i class="fas fa-map-marker-alt"></i> Delivery Address *</label>
                    <textarea class="form-control" id="delivery_address" name="delivery_address" 
                              rows="3" required placeholder="Enter complete delivery address..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number *</label>
                    <input type="text" class="form-control" id="phone" name="phone" 
                           required placeholder="07XX XXX XXX">
                </div>
                
                <div class="form-group">
                    <label for="status"><i class="fas fa-flag"></i> Delivery Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="pending" selected>⏳ Pending</option>
                        <option value="assigned">👤 Assigned</option>
                        <option value="picked_up">📦 Picked Up</option>
                        <option value="in_transit">🚚 In Transit</option>
                        <option value="delivered">✅ Delivered</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="delivery_notes"><i class="fas fa-sticky-note"></i> Additional Notes</label>
                    <textarea class="form-control" id="delivery_notes" name="delivery_notes" 
                              rows="3" placeholder="If there are special instructions, write them here..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Create Delivery
                    </button>
                    <a href="{{ route('deliveries.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.select-order {
    font-size: 1rem;
    padding: 0.75rem;
}

.order-preview {
    background: linear-gradient(135deg, rgba(251, 175, 50, 0.1), rgba(113, 154, 10, 0.1));
    padding: 1.5rem;
    border-radius: 10px;
    margin: 1.5rem 0;
    border-left: 4px solid #fbaf32;
}

.order-preview h4 {
    color: #454545;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.preview-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.preview-item label {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 600;
    margin: 0;
}

.preview-item span {
    color: #454545;
    font-size: 1rem;
}

.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-text {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

.text-muted {
    color: #6c757d;
}

@media (max-width: 768px) {
    .grid-2 {
        grid-template-columns: 1fr;
    }
    
    .preview-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
handleFormSubmit('deliveryForm');

// Auto-fill delivery information when order is selected
const orderSelect = document.getElementById('order_id');
const orderPreview = document.getElementById('orderPreview');

orderSelect.addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    
    if (option.value) {
        // Fill in the form fields
        document.getElementById('delivery_address').value = option.dataset.address || '';
        document.getElementById('phone').value = option.dataset.phone || '';
        
        // Show order preview
        orderPreview.style.display = 'block';
        document.getElementById('previewCustomer').textContent = option.dataset.customer || '-';
        document.getElementById('previewAmount').textContent = 'TZS ' + parseFloat(option.dataset.amount || 0).toLocaleString();
        document.getElementById('previewAddress').textContent = option.dataset.address || '-';
        document.getElementById('previewPhone').textContent = option.dataset.phone || '-';
        
        // Auto-suggest delivery fee based on amount (example: 10% of order or minimum 2000)
        const orderAmount = parseFloat(option.dataset.amount || 0);
        const suggestedFee = Math.max(2000, Math.ceil(orderAmount * 0.1 / 1000) * 1000);
        document.getElementById('delivery_fee').value = suggestedFee;
        
    } else {
        // Hide preview if no order selected
        orderPreview.style.display = 'none';
        document.getElementById('delivery_address').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('delivery_fee').value = 0;
    }
});

// Auto-update status when delivery person is assigned
const deliveryPersonSelect = document.getElementById('delivery_person_id');
const statusSelect = document.getElementById('status');

deliveryPersonSelect.addEventListener('change', function() {
    if (this.value && statusSelect.value === 'pending') {
        statusSelect.value = 'assigned';
    } else if (!this.value && statusSelect.value === 'assigned') {
        statusSelect.value = 'pending';
    }
});
</script>
@endpush