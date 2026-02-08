@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>🛒 My Cart</h1>
    </div>
    
    <div class="cart-container">
        <div class="cart-items">
            <div class="card">
                <div class="card-header">
                    <h2>Cart Items</h2>
                    @if(count($cart) > 0)
                        <button class="btn btn-sm btn-danger" onclick="clearCart()">
                            Clear Cart
                        </button>
                    @endif
                </div>
                
                @if(count($cart) > 0)
                    <div class="cart-list">
                        @foreach($cart as $item)
                        <div class="cart-item" id="cart-item-{{ $item['id'] }}">
                            <div class="cart-item-image">
                                @if($item['image'])
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}">
                                @else
                                    <img src="https://via.placeholder.com/100?text=Food" alt="{{ $item['name'] }}">
                                @endif
                            </div>
                            
                            <div class="cart-item-details">
                                <h3>{{ $item['name'] }}</h3>
                                <p class="cart-item-price">TZS {{ number_format($item['price'], 0) }}</p>
                            </div>
                            
                            <div class="cart-item-quantity">
                                <button onclick="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})">-</button>
                                <input 
                                    type="number" 
                                    value="{{ $item['quantity'] }}" 
                                    min="1" 
                                    id="qty-{{ $item['id'] }}"
                                    onchange="updateQuantity({{ $item['id'] }}, this.value)"
                                >
                                <button onclick="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})">+</button>
                            </div>
                            
                            <div class="cart-item-subtotal">
                                <p>Subtotal</p>
                                <h3 id="subtotal-{{ $item['id'] }}">TZS {{ number_format($item['price'] * $item['quantity'], 0) }}</h3>
                            </div>
                            
                            <button class="cart-item-remove" onclick="removeFromCart({{ $item['id'] }})">
                                🗑️
                            </button>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <h3>Your cart is empty</h3>
                        <p>Start adding delicious items to your cart!</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            Browse Menu
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        @if(count($cart) > 0)
        <div class="cart-summary">
            <div class="card">
                <div class="card-header">
                    <h2>Order Summary</h2>
                </div>
                
                <div class="summary-content">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="cart-total">TZS {{ number_format($total, 0) }}</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Delivery Fee</span>
                        <span>TZS 0</span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span id="final-total">TZS {{ number_format($total, 0) }}</span>
                    </div>
                    
                    <a href="{{ route('checkout') }}" class="btn btn-primary btn-block">
                        Proceed to Checkout
                    </a>
                    
                    <a href="{{ route('products.index') }}" class="btn btn-outline btn-block">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .cart-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .cart-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .cart-item {
        display: grid;
        grid-template-columns: 100px 1fr auto auto auto;
        gap: 1.5rem;
        align-items: center;
        padding: 1.5rem;
        background: var(--light-color);
        border-radius: 8px;
    }
    
    .cart-item-image img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .cart-item-details h3 {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }
    
    .cart-item-price {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .cart-item-quantity {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    .cart-item-quantity button {
        width: 35px;
        height: 35px;
        border: 2px solid var(--primary-color);
        background: white;
        color: var(--primary-color);
        border-radius: 6px;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s;
    }
    
    .cart-item-quantity button:hover {
        background: var(--primary-color);
        color: white;
    }
    
    .cart-item-quantity input {
        width: 60px;
        text-align: center;
        padding: 8px;
        border: 2px solid var(--border-color);
        border-radius: 6px;
    }
    
    .cart-item-subtotal {
        text-align: right;
    }
    
    .cart-item-subtotal p {
        font-size: 0.875rem;
        color: #636e72;
        margin-bottom: 0.25rem;
    }
    
    .cart-item-subtotal h3 {
        color: var(--primary-color);
        font-size: 1.25rem;
    }
    
    .cart-item-remove {
        background: var(--danger-color);
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .cart-item-remove:hover {
        background: #c0392b;
    }
    
    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .empty-cart-icon {
        font-size: 5rem;
        margin-bottom: 1rem;
    }
    
    .empty-cart h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .empty-cart p {
        color: #636e72;
        margin-bottom: 1.5rem;
    }
    
    .summary-content {
        padding: 1.5rem;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        font-size: 1rem;
    }
    
    .summary-divider {
        border-top: 2px solid var(--border-color);
        margin: 1.5rem 0;
    }
    
    .summary-total {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    
    .btn-block {
        width: 100%;
        margin-top: 1rem;
    }
    
    @media (max-width: 768px) {
        .cart-container {
            grid-template-columns: 1fr;
        }
        
        .cart-item {
            grid-template-columns: 80px 1fr;
            gap: 1rem;
        }
        
        .cart-item-quantity,
        .cart-item-subtotal {
            grid-column: 1 / -1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
async function updateQuantity(productId, newQuantity) {
    if (newQuantity < 1) return;
    
    try {
        const response = await fetchAPI('/cart/update', {
            method: 'POST',
            body: JSON.stringify({
                product_id: productId,
                quantity: parseInt(newQuantity)
            })
        });
        
        if (response.success) {
            // Update quantity input
            document.getElementById(`qty-${productId}`).value = newQuantity;
            
            // Update subtotal
            document.getElementById(`subtotal-${productId}`).textContent = 
                'TZS ' + response.subtotal.toLocaleString();
            
            // Update cart total
            document.getElementById('cart-total').textContent = 
                'TZS ' + response.total.toLocaleString();
            document.getElementById('final-total').textContent = 
                'TZS ' + response.total.toLocaleString();
            
            showToast(response.message, 'success');
        }
    } catch (error) {
        showToast(error.message || 'Failed to update cart', 'error');
    }
}

async function removeFromCart(productId) {
    if (!confirm('Remove this item from cart?')) return;
    
    try {
        const response = await fetchAPI('/cart/remove', {
            method: 'POST',
            body: JSON.stringify({
                product_id: productId
            })
        });
        
        if (response.success) {
            // Remove item from DOM
            document.getElementById(`cart-item-${productId}`).remove();
            
            // Update cart badge
            updateCartBadge();
            
            // If cart is empty, reload page
            if (response.cart_count === 0) {
                location.reload();
            } else {
                // Update totals
                document.getElementById('cart-total').textContent = 
                    'TZS ' + response.total.toLocaleString();
                document.getElementById('final-total').textContent = 
                    'TZS ' + response.total.toLocaleString();
            }
            
            showToast(response.message, 'success');
        }
    } catch (error) {
        showToast(error.message || 'Failed to remove item', 'error');
    }
}

async function clearCart() {
    if (!confirm('Are you sure you want to clear your cart?')) return;
    
    try {
        const response = await fetchAPI('/cart/clear', {
            method: 'POST'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            updateCartBadge();
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to clear cart', 'error');
    }
}
</script>
@endpush