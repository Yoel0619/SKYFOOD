@extends('layouts.app')

@section('title', 'Create Delivery')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-plus"></i> Create New Delivery</h1>
        <a href="{{ route('deliveries.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="form-container">
        <form id="deliveryForm" action="{{ route('deliveries.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="order_id"><i class="fas fa-shopping-cart"></i> Order *</label>
                <select class="form-control" id="order_id" name="order_id" required>
                    <option value="">Select Order</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}" 
                                data-address="{{ $order->delivery_address }}"
                                data-phone="{{ $order->phone }}">
                            {{ $order->order_number }} - {{ $order->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="delivery_person_id"><i class="fas fa-user"></i> Delivery Person</label>
                    <select class="form-control" id="delivery_person_id" name="delivery_person_id">
                        <option value="">Not Assigned Yet</option>
                        @foreach($deliveryPersons as $person)
                            <option value="{{ $person->id }}">{{ $person->name }}</option>
                        @endforeach
                    </select>
                    <small style="color: #757575;">Create a 'delivery' role first if no delivery persons available</small>
                </div>
                
                <div class="form-group">
                    <label for="delivery_fee"><i class="fas fa-money-bill"></i> Delivery Fee (TZS) *</label>
                    <input 
                        type="number" 
                        class="form-control" 
                        id="delivery_fee" 
                        name="delivery_fee" 
                        value="0"
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
                    placeholder="Full delivery address"
                    required
                ></textarea>
            </div>
            
            <div class="form-group">
                <label for="phone"><i class="fas fa-phone"></i> Phone Number *</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="phone" 
                    name="phone" 
                    placeholder="0712345678"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="status"><i class="fas fa-check-circle"></i> Status *</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="pending" selected>⏳ Pending</option>
                    <option value="assigned">👤 Assigned</option>
                    <option value="picked_up">📦 Picked Up</option>
                    <option value="in_transit">🚚 In Transit</option>
                    <option value="delivered">✅ Delivered</option>
                    <option value="cancelled">❌ Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="delivery_notes"><i class="fas fa-sticky-note"></i> Delivery Notes</label>
                <textarea 
                    class="form-control" 
                    id="delivery_notes" 
                    name="delivery_notes" 
                    rows="3"
                    placeholder="Special delivery instructions..."
                ></textarea>
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
@endsection

@push('scripts')
<script>
handleFormSubmit('deliveryForm');

// Auto-fill address and phone when order is selected
document.getElementById('order_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        const address = selectedOption.getAttribute('data-address');
        const phone = selectedOption.getAttribute('data-phone');
        
        if (address) {
            document.getElementById('delivery_address').value = address;
        }
        if (phone) {
            document.getElementById('phone').value = phone;
        }
    }
});
</script>
@endpush