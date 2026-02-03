@extends('layouts.app')

@section('title', 'Checkout - SKYFOOD')

@section('content')
<div class="checkout-page">
    <div class="container">
        <div class="page-header">
            <h1>Checkout</h1>
            <p>Complete your order</p>
        </div>

        <div class="checkout-content">
            <!-- Checkout Form -->
            <div class="checkout-form-section">
                <form id="checkoutForm" class="checkout-form">
                    @csrf
                    
                    <div id="errorMessage" class="alert alert-error" style="display: none;"></div>
                    
                    <div class="form-section">
                        <h3>Delivery Information</h3>
                        
                        <div class="form-group">
                            <label for="delivery_address">Delivery Address *</label>
                            <textarea id="delivery_address" name="delivery_address" class="form-control" 
                                      rows="3" required placeholder="Enter your full delivery address"></textarea>
                            <span class="error-text" id="delivery_address-error"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" class="form-control" 
                                   value="{{ Auth::user()->phone ?? '' }}" required
                                   placeholder="+255...">
                            <span class="error-text" id="phone-error"></span>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Payment Method</h3>
                        
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cash" checked>
                                <div class="payment-card">
                                    <div class="payment-icon">💵</div>
                                    <div class="payment-details">
                                        <strong>Cash on Delivery</strong>
                                        <p>Pay when you receive your order</p>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="mobile_money">
                                <div class="payment-card">
                                    <div class="payment-icon">📱</div>
                                    <div class="payment-details">
                                        <strong>Mobile Money</strong>
                                        <p>M-Pesa, Tigo Pesa, Airtel Money</p>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card">
                                <div class="payment-card">
                                    <div class="payment-icon">💳</div>
                                    <div class="payment-details">
                                        <strong>Credit/Debit Card</strong>
                                        <p>Visa, Mastercard accepted</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Additional Notes (Optional)</h3>
                        
                        <div class="form-group">
                            <textarea id="notes" name="notes" class="form-control" rows="3" 
                                      placeholder="Any special instructions for your order?"></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="placeOrderBtn">
                        <span id="placeOrderText">Place Order</span>
                        <span id="placeOrderLoader" class="btn-loader" style="display: none;">
                            <span class="spinner"></span> Processing...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="checkout-summary">
                <h3>Order Summary</h3>
                
                <div id="orderItems" class="summary-items">
                    <!-- Items will be loaded here -->
                </div>
                
                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="summarySubtotal">TZS 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Delivery Fee:</span>
                        <span id="summaryDeliveryFee">TZS 2,000</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="summaryTotal">TZS 0</span>
                    </div>
                </div>
                
                <div class="summary-info">
                    <p><strong>Estimated Delivery:</strong> 30-45 minutes</p>
                    <p><strong>Restaurant:</strong> <span id="restaurantName">-</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cart = [];
const DELIVERY_FEE = 2000;

document.addEventListener('DOMContentLoaded', function() {
    loadCartSummary();
    
    document.getElementById('checkoutForm').addEventListener('submit', handleCheckout);
});

// Load cart summary
function loadCartSummary() {
    cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    if (cart.length === 0) {
        alert('Your cart is empty!');
        window.location.href = '{{ route('menu.index') }}';
        return;
    }
    
    // Check if all items are from same restaurant
    const restaurantIds = [...new Set(cart.map(item => item.restaurant_id))];
    if (restaurantIds.length > 1) {
        alert('All items must be from the same restaurant. Please check your cart.');
        window.location.href = '{{ route('cart.index') }}';
        return;
    }
    
    displayOrderSummary();
}

// Display order summary
function displayOrderSummary() {
    const orderItemsEl = document.getElementById('orderItems');
    const restaurantNameEl = document.getElementById('restaurantName');
    
    if (cart.length > 0) {
        restaurantNameEl.textContent = cart[0].restaurant_name;
    }
    
    orderItemsEl.innerHTML = cart.map(item => `
        <div class="summary-item">
            <div class="summary-item-info">
                <span class="summary-item-name">${item.name}</span>
                <span class="summary-item-qty">x${item.quantity}</span>
            </div>
            <span class="summary-item-price">TZS ${formatPrice(item.price * item.quantity)}</span>
        </div>
    `).join('');
    
    updateCheckoutSummary();
}

// Update summary totals
function updateCheckoutSummary() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const total = subtotal + DELIVERY_FEE;
    
    document.getElementById('summarySubtotal').textContent = `TZS ${formatPrice(subtotal)}`;
    document.getElementById('summaryDeliveryFee').textContent = `TZS ${formatPrice(DELIVERY_FEE)}`;
    document.getElementById('summaryTotal').textContent = `TZS ${formatPrice(total)}`;
}

// Handle checkout
async function handleCheckout(e) {
    e.preventDefault();
    
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    const placeOrderText = document.getElementById('placeOrderText');
    const placeOrderLoader = document.getElementById('placeOrderLoader');
    const errorMessage = document.getElementById('errorMessage');
    
    // Clear previous errors
    document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
    errorMessage.style.display = 'none';
    
    // Disable button
    placeOrderBtn.disabled = true;
    placeOrderText.style.display = 'none';
    placeOrderLoader.style.display = 'inline-block';
    
    // Prepare order data
    const orderData = {
        items: cart.map(item => ({
            menu_item_id: item.id,
            quantity: item.quantity
        })),
        delivery_address: document.getElementById('delivery_address').value,
        phone: document.getElementById('phone').value,
        payment_method: document.querySelector('input[name="payment_method"]:checked').value,
        notes: document.getElementById('notes').value
    };
    
    try {
        const response = await fetch('/api/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(orderData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Clear cart
            localStorage.removeItem('cart');
            updateCartCount();
            
            // Show success and redirect
            alert('Order placed successfully! Order ID: #' + data.order.id);
            window.location.href = '{{ route('orders.index') }}';
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorEl = document.getElementById(`${field}-error`);
                    if (errorEl) {
                        errorEl.textContent = data.errors[field][0];
                    }
                });
                errorMessage.textContent = 'Please fix the errors below.';
            } else {
                errorMessage.textContent = data.message || 'Failed to place order.';
            }
            errorMessage.style.display = 'block';
            
            // Re-enable button
            placeOrderBtn.disabled = false;
            placeOrderText.style.display = 'inline-block';
            placeOrderLoader.style.display = 'none';
        }
    } catch (error) {
        errorMessage.textContent = 'An error occurred. Please try again.';
        errorMessage.style.display = 'block';
        
        // Re-enable button
        placeOrderBtn.disabled = false;
        placeOrderText.style.display = 'inline-block';
        placeOrderLoader.style.display = 'none';
        
        console.error('Error:', error);
    }
}

// Update cart count
function updateCartCount() {
    const cartCountEl = document.getElementById('cartCount');
    if (cartCountEl) {
        cartCountEl.textContent = '0';
    }
}

// Format price
function formatPrice(price) {
    return parseFloat(price).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}
</script>
@endpush