@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-edit"></i> Edit Order</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="form-container">
        <div class="card">
            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> Order Number:</strong> {{ $order->order_number }}
            </div>
            
            <form id="orderForm" action="{{ route('orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Customer Info (Read-only) -->
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Customer</label>
                    <input type="text" class="form-control" value="{{ $order->user->name }} ({{ $order->user->email }})" readonly>
                </div>
                
                <!-- Order Items -->
                <div class="form-group">
                    <label><i class="fas fa-utensils"></i> Order Items *</label>
                    <div id="orderItems">
                        @foreach($order->orderItems as $index => $item)
                        <div class="order-item-row" id="item-{{ $index }}">
                            <select class="form-control" name="products[{{ $index }}][product_id]" required onchange="updatePrice({{ $index }})">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-price="{{ $product->price }}"
                                            data-stock="{{ $product->stock }}"
                                            {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} - TZS {{ number_format($product->price, 0) }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="number" class="form-control" name="products[{{ $index }}][quantity]" 
                                   placeholder="Qty" min="1" value="{{ $item->quantity }}" required onchange="updateTotal()">
                            <input type="number" class="form-control" name="products[{{ $index }}][price]" 
                                   value="{{ $item->price }}" readonly required>
                            <button type="button" class="btn btn-danger" onclick="removeItem({{ $index }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="addItem()" style="margin-top: 1rem;">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>
                
                <!-- Delivery Information -->
                <div class="form-group">
                    <label for="delivery_address"><i class="fas fa-map-marker-alt"></i> Delivery Address *</label>
                    <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3" required>{{ $order->delivery_address }}</textarea>
                </div>
                
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number *</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $order->phone }}" required>
                </div>
                
                <div class="form-group">
                    <label for="notes"><i class="fas fa-sticky-note"></i> Order Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ $order->notes }}</textarea>
                </div>
                
                <!-- Order Status -->
                <div class="form-group">
                    <label for="status"><i class="fas fa-check-circle"></i> Order Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <!-- Total Amount Display -->
                <div class="order-total">
                    <h3>Total Amount: TZS <span id="totalAmount">{{ number_format($order->total_amount, 0) }}</span></h3>
                    <input type="hidden" id="total_amount" name="total_amount" value="{{ $order->total_amount }}">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Order
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="button" class="btn btn-danger" onclick="deleteItem('/orders/{{ $order->id }}', 'Delete order {{ $order->order_number }}?')">
                        <i class="fas fa-trash"></i> Delete Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.order-item-row {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 1rem;
    margin-bottom: 1rem;
}

.order-total {
    background: linear-gradient(135deg, rgba(251, 175, 50, 0.1), rgba(113, 154, 10, 0.1));
    padding: 1.5rem;
    border-radius: 10px;
    margin: 2rem 0;
    text-align: center;
}

.order-total h3 {
    margin: 0;
    color: #454545;
    font-size: 1.5rem;
}

.order-total span {
    color: #fbaf32;
    font-weight: 900;
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

@media (max-width: 768px) {
    .order-item-row {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
handleFormSubmit('orderForm');

let itemCount = {{ $order->orderItems->count() }};

function addItem() {
    const container = document.getElementById('orderItems');
    const newItem = `
        <div class="order-item-row" id="item-${itemCount}">
            <select class="form-control" name="products[${itemCount}][product_id]" required onchange="updatePrice(${itemCount})">
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                            data-price="{{ $product->price }}"
                            data-stock="{{ $product->stock }}">
                        {{ $product->name }} - TZS {{ number_format($product->price, 0) }}
                    </option>
                @endforeach
            </select>
            <input type="number" class="form-control" name="products[${itemCount}][quantity]" 
                   placeholder="Qty" min="1" value="1" required onchange="updateTotal()">
            <input type="number" class="form-control" name="products[${itemCount}][price]" 
                   placeholder="Price" readonly required>
            <button type="button" class="btn btn-danger" onclick="removeItem(${itemCount})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', newItem);
    itemCount++;
}

function removeItem(index) {
    const item = document.getElementById(`item-${index}`);
    if (item) {
        item.remove();
        updateTotal();
    }
}

function updatePrice(index) {
    const row = document.getElementById(`item-${index}`);
    if (!row) return;
    
    const select = row.querySelector('select');
    const priceInput = row.querySelector('input[name*="[price]"]');
    
    const option = select.options[select.selectedIndex];
    if (option.value) {
        const price = option.dataset.price;
        priceInput.value = price;
        updateTotal();
    }
}

function updateTotal() {
    let total = 0;
    const rows = document.querySelectorAll('.order-item-row');
    
    rows.forEach(row => {
        const price = parseFloat(row.querySelector('input[name*="[price]"]').value) || 0;
        const quantity = parseInt(row.querySelector('input[name*="[quantity]"]').value) || 0;
        total += price * quantity;
    });
    
    document.getElementById('totalAmount').textContent = total.toLocaleString();
    document.getElementById('total_amount').value = total;
}

// Calculate initial total
updateTotal();
</script>
@endpush