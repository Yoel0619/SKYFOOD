@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-shopping-cart"></i> Unda Oda Mpya</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Rudi
        </a>
    </div>
    
    <div class="order-creation-wrapper">
        <!-- Products Selection Section -->
        <div class="products-section">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-utensils"></i> Chagua Vyakula</h3>
                </div>
                
                <!-- Category Filter -->
                <div class="category-filter">
                    <button class="category-btn active" onclick="filterProducts('all')">
                        <i class="fas fa-th"></i> Vyote
                    </button>
                    @foreach($categories as $category)
                    <button class="category-btn" onclick="filterProducts('{{ $category->id }}')">
                        {{ $category->name }}
                    </button>
                    @endforeach
                </div>
                
                <!-- Products Grid -->
                <div class="products-grid" id="productsGrid">
                    @foreach($products as $product)
                    <div class="product-card" data-category="{{ $product->category_id }}" data-product-id="{{ $product->id }}">
                        <div class="product-image">
                            @if($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}">
                            @else
                            <div class="no-image"><i class="fas fa-utensils"></i></div>
                            @endif
                            @if($product->stock < 10)
                            <span class="stock-badge low">Stock: {{ $product->stock }}</span>
                            @elseif($product->stock == 0)
                            <span class="stock-badge out">Hakuna Stock</span>
                            @endif
                        </div>
                        <div class="product-info">
                            <h4>{{ $product->name }}</h4>
                            <p class="product-desc">{{ Str::limit($product->description, 60) }}</p>
                            <div class="product-footer">
                                <span class="price">TSh {{ number_format($product->price, 0) }}</span>
                                @if($product->stock > 0)
                                <button type="button" class="btn-add-to-cart" 
                                        onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock }})"
                                        data-product="{{ $product->id }}">
                                    <i class="fas fa-plus"></i> Ongeza
                                </button>
                                @else
                                <button type="button" class="btn-add-to-cart" disabled>
                                    <i class="fas fa-times"></i> Hakuna
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Cart/Order Summary Section -->
        <div class="cart-section">
            <div class="card sticky-cart">
                <div class="card-header">
                    <h3><i class="fas fa-shopping-basket"></i> Oda Yako</h3>
                    <span class="cart-count" id="cartCount">0 items</span>
                </div>
                
                <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    
                    <!-- Cart Items -->
                    <div class="cart-items" id="cartItems">
                        <div class="empty-cart">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Oda yako bado tupu</p>
                            <small>Chagua vyakula kutoka upande wa kushoto</small>
                        </div>
                    </div>
                    
                    <!-- Delivery Information -->
                    <div class="delivery-info" id="deliveryInfo" style="display: none;">
                        <h4><i class="fas fa-map-marker-alt"></i> Taarifa za Uwasilishaji</h4>
                        
                        @if(auth()->user()->isAdmin())
                        <div class="form-group">
                            <label for="user_id">Mteja *</label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">Chagua Mteja</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                            data-phone="{{ $customer->phone }}"
                                            data-address="{{ $customer->address }}">
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <div class="form-group">
                            <label for="delivery_address">Anwani *</label>
                            <textarea class="form-control" id="delivery_address" name="delivery_address" 
                                      rows="2" required 
                                      placeholder="Weka anwani kamili ya uwasilishaji">{{ auth()->user()->address }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Namba ya Simu *</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="{{ auth()->user()->phone }}" required 
                                   placeholder="0712345678">
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Maelezo ya Ziada (Si Lazima)</label>
                            <textarea class="form-control" id="notes" name="notes" 
                                      rows="2" placeholder="Kama una maombi maalum, andika hapa..."></textarea>
                        </div>
                        
                        @if(auth()->user()->isAdmin())
                        <div class="form-group">
                            <label for="status">Hali ya Oda *</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" selected>Inasubiri</option>
                                <option value="processing">Inatengenezwa</option>
                                <option value="completed">Imekamilika</option>
                                <option value="cancelled">Imesitishwa</option>
                            </select>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Order Total -->
                    <div class="order-total" id="orderTotal" style="display: none;">
                        <div class="total-row">
                            <span>Jumla ya Bidhaa:</span>
                            <span id="subtotalAmount">TSh 0</span>
                        </div>
                        <div class="total-row delivery">
                            <span>Ada ya Usafirishaji:</span>
                            <span>TSh 0</span>
                        </div>
                        <div class="total-row grand">
                            <span>Jumla:</span>
                            <span id="totalAmount">TSh 0</span>
                        </div>
                        <input type="hidden" id="total_amount" name="total_amount" value="0">
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="cart-actions" id="cartActions" style="display: none;">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-check"></i> Thibitisha Oda
                        </button>
                        <button type="button" class="btn btn-outline btn-block" onclick="clearCart()">
                            <i class="fas fa-trash"></i> Futa Yote
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.order-creation-wrapper {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-top: 2rem;
}

/* Products Section */
.products-section {
    min-height: 500px;
}

.category-filter {
    display: flex;
    gap: 0.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    overflow-x: auto;
}

.category-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #dee2e6;
    background: white;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s;
    white-space: nowrap;
    font-size: 0.9rem;
    font-weight: 500;
}

.category-btn:hover {
    border-color: #fbaf32;
    color: #fbaf32;
    transform: translateY(-2px);
}

.category-btn.active {
    background: linear-gradient(135deg, #fbaf32, #719a0a);
    color: white;
    border-color: transparent;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.5rem;
}

.product-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.product-image {
    width: 100%;
    height: 180px;
    overflow: hidden;
    position: relative;
    background: #f8f9fa;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    font-size: 3rem;
    color: #dee2e6;
}

.stock-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #28a745;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
}

.stock-badge.low {
    background: #ffc107;
}

.stock-badge.out {
    background: #dc3545;
}

.product-info {
    padding: 1rem;
}

.product-info h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    color: #454545;
}

.product-desc {
    color: #777;
    font-size: 0.85rem;
    margin-bottom: 1rem;
    min-height: 40px;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.price {
    font-size: 1.2rem;
    font-weight: 700;
    color: #fbaf32;
}

.btn-add-to-cart {
    background: linear-gradient(135deg, #fbaf32, #719a0a);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-add-to-cart:hover:not(:disabled) {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(251, 175, 50, 0.4);
}

.btn-add-to-cart:disabled {
    background: #6c757d;
    cursor: not-allowed;
    opacity: 0.6;
}

/* Cart Section */
.cart-section {
    position: relative;
}

.sticky-cart {
    position: sticky;
    top: 100px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}

.cart-count {
    background: #fbaf32;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
}

.empty-cart {
    text-align: center;
    padding: 3rem 1rem;
    color: #adb5bd;
}

.empty-cart i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.cart-items {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 1rem;
}

.cart-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    align-items: center;
}

.cart-item-info {
    flex: 1;
}

.cart-item-name {
    font-weight: 600;
    color: #454545;
    margin-bottom: 0.25rem;
}

.cart-item-price {
    color: #fbaf32;
    font-size: 0.9rem;
}

.cart-item-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.qty-btn {
    width: 30px;
    height: 30px;
    border: 1px solid #dee2e6;
    background: white;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.qty-btn:hover {
    background: #fbaf32;
    color: white;
    border-color: #fbaf32;
}

.qty-display {
    min-width: 30px;
    text-align: center;
    font-weight: 600;
}

.remove-item {
    color: #dc3545;
    cursor: pointer;
    padding: 0.25rem;
    transition: all 0.2s;
}

.remove-item:hover {
    color: #c82333;
    transform: scale(1.1);
}

.delivery-info {
    padding: 1rem;
    border-top: 2px solid #e9ecef;
    margin-top: 1rem;
}

.delivery-info h4 {
    font-size: 1rem;
    margin-bottom: 1rem;
    color: #454545;
}

.order-total {
    background: linear-gradient(135deg, rgba(251, 175, 50, 0.1), rgba(113, 154, 10, 0.1));
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    color: #454545;
}

.total-row.delivery {
    color: #6c757d;
    font-size: 0.9rem;
}

.total-row.grand {
    border-top: 2px solid #dee2e6;
    margin-top: 0.5rem;
    padding-top: 1rem;
    font-size: 1.2rem;
    font-weight: 700;
    color: #454545;
}

.total-row.grand span:last-child {
    color: #fbaf32;
}

.cart-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

/* Responsive */
@media (max-width: 992px) {
    .order-creation-wrapper {
        grid-template-columns: 1fr;
    }
    
    .sticky-cart {
        position: static;
        max-height: none;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
}

@media (max-width: 576px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .category-filter {
        justify-content: flex-start;
    }
}
</style>
@endpush

@push('scripts')
<script>
handleFormSubmit('orderForm');

// Cart data
let cart = [];

// Auto-fill customer info (Admin only)
const userSelect = document.getElementById('user_id');
if (userSelect) {
    userSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            document.getElementById('phone').value = option.dataset.phone || '';
            document.getElementById('delivery_address').value = option.dataset.address || '';
        }
    });
}

// Filter products by category
function filterProducts(categoryId) {
    const products = document.querySelectorAll('.product-card');
    const buttons = document.querySelectorAll('.category-btn');
    
    // Update active button
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Filter products
    products.forEach(product => {
        if (categoryId === 'all' || product.dataset.category == categoryId) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Add product to cart
function addToCart(productId, productName, price, stock) {
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        if (existingItem.quantity < stock) {
            existingItem.quantity++;
        } else {
            showNotification('Stock hautosha!', 'warning');
            return;
        }
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: price,
            quantity: 1,
            stock: stock
        });
    }
    
    updateCartDisplay();
    showNotification('Imeongezwa kwenye oda!', 'success');
}

// Update cart quantity
function updateQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (!item) return;
    
    const newQuantity = item.quantity + change;
    
    if (newQuantity <= 0) {
        removeFromCart(productId);
        return;
    }
    
    if (newQuantity > item.stock) {
        showNotification('Stock hautosha!', 'warning');
        return;
    }
    
    item.quantity = newQuantity;
    updateCartDisplay();
}

// Remove item from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCartDisplay();
    showNotification('Imeondolewa!', 'info');
}

// Clear entire cart
function clearCart() {
    if (confirm('Je, una uhakika unataka kufuta yote?')) {
        cart = [];
        updateCartDisplay();
        showNotification('Oda imefutwa!', 'info');
    }
}

// Update cart display
function updateCartDisplay() {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const deliveryInfo = document.getElementById('deliveryInfo');
    const orderTotal = document.getElementById('orderTotal');
    const cartActions = document.getElementById('cartActions');
    
    // Update count
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = `${totalItems} item${totalItems !== 1 ? 's' : ''}`;
    
    // Clear existing items
    cartItemsContainer.innerHTML = '';
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Oda yako bado tupu</p>
                <small>Chagua vyakula kutoka upande wa kushoto</small>
            </div>
        `;
        deliveryInfo.style.display = 'none';
        orderTotal.style.display = 'none';
        cartActions.style.display = 'none';
        return;
    }
    
    // Show delivery info and totals
    deliveryInfo.style.display = 'block';
    orderTotal.style.display = 'block';
    cartActions.style.display = 'block';
    
    // Display cart items
    cart.forEach(item => {
        const itemHTML = `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">TSh ${item.price.toLocaleString()}</div>
                </div>
                <div class="cart-item-controls">
                    <button type="button" class="qty-btn" onclick="updateQuantity(${item.id}, -1)">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="qty-display">${item.quantity}</span>
                    <button type="button" class="qty-btn" onclick="updateQuantity(${item.id}, 1)">
                        <i class="fas fa-plus"></i>
                    </button>
                    <i class="fas fa-trash remove-item" onclick="removeFromCart(${item.id})"></i>
                </div>
            </div>
        `;
        cartItemsContainer.insertAdjacentHTML('beforeend', itemHTML);
    });
    
    // Calculate and update totals
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    document.getElementById('subtotalAmount').textContent = `TSh ${subtotal.toLocaleString()}`;
    document.getElementById('totalAmount').textContent = `TSh ${subtotal.toLocaleString()}`;
    document.getElementById('total_amount').value = subtotal;
}

// Handle form submission
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (cart.length === 0) {
        showNotification('Tafadhali ongeza bidhaa kwenye oda!', 'error');
        return;
    }
    
    // Create hidden inputs for cart items
    const form = this;
    
    // Remove any existing product inputs
    const existingInputs = form.querySelectorAll('input[name^="products"]');
    existingInputs.forEach(input => input.remove());
    
    // Add cart items to form
    cart.forEach((item, index) => {
        const productIdInput = document.createElement('input');
        productIdInput.type = 'hidden';
        productIdInput.name = `products[${index}][product_id]`;
        productIdInput.value = item.id;
        form.appendChild(productIdInput);
        
        const quantityInput = document.createElement('input');
        quantityInput.type = 'hidden';
        quantityInput.name = `products[${index}][quantity]`;
        quantityInput.value = item.quantity;
        form.appendChild(quantityInput);
        
        const priceInput = document.createElement('input');
        priceInput.type = 'hidden';
        priceInput.name = `products[${index}][price]`;
        priceInput.value = item.price;
        form.appendChild(priceInput);
    });
    
    // Submit the form
    form.submit();
});

// Notification helper
function showNotification(message, type = 'info') {
    // You can implement a toast notification here
    // For now, we'll use a simple alert
    console.log(`${type.toUpperCase()}: ${message}`);
}
</script>
@endpush