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
                                    <img src="https://via.placeholder.com/80?text=Food" alt="{{ $item['name'] }}">
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
                                <h3 id="subtotal-{{ $item['id'] }}">
                                    TZS {{ number_format($item['price'] * $item['quantity'], 0) }}
                                </h3>
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
/* ===== COMPACT CART DESIGN ===== */

.cart-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1rem;
    margin-top: 1rem;
}

.cart-list {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto auto;
    gap: 1rem;
    align-items: center;
    padding: 1rem;
    background: var(--light-color);
    border-radius: 6px;
}

.cart-item-image img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
}

.cart-item-details h3 {
    font-size: 0.95rem;
    margin-bottom: 0.3rem;
}

.cart-item-price {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--primary-color);
}

.cart-item-quantity {
    display: flex;
    gap: 0.3rem;
    align-items: center;
}

.cart-item-quantity button {
    width: 28px;
    height: 28px;
    font-size: 1rem;
    border: 1px solid var(--primary-color);
    background: #fff;
    color: var(--primary-color);
    border-radius: 4px;
    cursor: pointer;
}

.cart-item-quantity input {
    width: 45px;
    padding: 5px;
    font-size: 0.85rem;
    text-align: center;
}

.cart-item-subtotal p {
    font-size: 0.75rem;
    margin-bottom: 2px;
}

.cart-item-subtotal h3 {
    font-size: 1rem;
    color: var(--primary-color);
}

.cart-item-remove {
    background: var(--danger-color);
    color: #fff;
    border: none;
    padding: 6px 10px;
    font-size: 0.9rem;
    border-radius: 4px;
    cursor: pointer;
}

.empty-cart {
    text-align: center;
    padding: 2rem 1rem;
}

.empty-cart-icon {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.summary-content {
    padding: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.7rem;
    font-size: 0.9rem;
}

.summary-divider {
    border-top: 1px solid var(--border-color);
    margin: 1rem 0;
}

.summary-total {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--primary-color);
}

.btn-block {
    width: 100%;
    margin-top: 0.7rem;
    padding: 8px;
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
    .cart-container {
        grid-template-columns: 1fr;
    }
    
    .cart-item {
        grid-template-columns: 70px 1fr;
        gap: 0.8rem;
    }
    
    .cart-item-quantity,
    .cart-item-subtotal {
        grid-column: 1 / -1;
    }
}
</style>
@endpush
