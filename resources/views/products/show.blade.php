@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container">
    <div class="product-detail">
        <div class="product-detail-image">
            @if($product->image)
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
            @else
                <img src="https://via.placeholder.com/600x400?text={{ urlencode($product->name) }}" alt="{{ $product->name }}">
            @endif
        </div>
        
        <div class="product-detail-info">
            <div class="breadcrumb">
                <a href="{{ route('products.index') }}">Menu</a>
                <span>/</span>
                <span>{{ $product->category->name }}</span>
                <span>/</span>
                <strong>{{ $product->name }}</strong>
            </div>
            
            <h1>{{ $product->name }}</h1>
            
            <div class="product-meta">
                <span class="badge badge-primary">{{ $product->category->name }}</span>
                @if($product->status == 'available')
                    <span class="badge badge-success">Available</span>
                @else
                    <span class="badge badge-danger">Unavailable</span>
                @endif
            </div>
            
            <div class="product-price-large">
                <span>TZS {{ number_format($product->price, 0) }}</span>
            </div>
            
            <div class="product-stock">
                @if($product->stock > 0)
                    <span class="stock-available">✅ In Stock ({{ $product->stock }} available)</span>
                @else
                    <span class="stock-out">❌ Out of Stock</span>
                @endif
            </div>
            
            <div class="product-description-full">
                <h3>Description</h3>
                <p>{{ $product->description ?? 'No description available' }}</p>
            </div>
            
            @if(auth()->user()->isCustomer())
                @if($product->status == 'available' && $product->stock > 0)
                    <div class="quantity-selector">
                        <label>Quantity:</label>
                        <div class="quantity-controls">
                            <button onclick="decreaseQuantity()">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock }}">
                            <button onclick="increaseQuantity()">+</button>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary btn-lg" onclick="addToCartWithQuantity()">
                        🛒 Add to Cart
                    </button>
                @else
                    <button class="btn btn-secondary btn-lg" disabled>
                        Currently Unavailable
                    </button>
                @endif
            @endif
            
            @if(auth()->user()->isAdmin())
                <div class="admin-actions">
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
                        ✏️ Edit Product
                    </a>
                    <button class="btn btn-danger" onclick="deleteProduct({{ $product->id }})">
                        🗑️ Delete Product
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-detail {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        margin-top: 2rem;
    }
    
    .product-detail-image img {
        width: 100%;
        border-radius: 12px;
        box-shadow: var(--shadow);
    }
    
    .breadcrumb {
        margin-bottom: 1rem;
        color: #636e72;
        font-size: 0.875rem;
    }
    
    .breadcrumb a {
        color: var(--primary-color);
        text-decoration: none;
    }
    
    .breadcrumb span {
        margin: 0 0.5rem;
    }
    
    .product-detail-info h1 {
        font-size: 2.5rem;
        color: var(--dark-color);
        margin-bottom: 1rem;
    }
    
    .product-meta {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .product-price-large {
        font-size: 3rem;
        font-weight: bold;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    
    .product-stock {
        margin-bottom: 1.5rem;
    }
    
    .stock-available {
        color: var(--success-color);
        font-weight: 600;
    }
    
    .stock-out {
        color: var(--danger-color);
        font-weight: 600;
    }
    
    .product-description-full {
        background: var(--light-color);
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .product-description-full h3 {
        margin-bottom: 0.5rem;
    }
    
    .quantity-selector {
        margin-bottom: 1.5rem;
    }
    
    .quantity-selector label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .quantity-controls {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    .quantity-controls button {
        width: 40px;
        height: 40px;
        border: 2px solid var(--primary-color);
        background: white;
        color: var(--primary-color);
        border-radius: 8px;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .quantity-controls button:hover {
        background: var(--primary-color);
        color: white;
    }
    
    .quantity-controls input {
        width: 80px;
        text-align: center;
        padding: 10px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 1.1rem;
    }
    
    .btn-lg {
        padding: 15px 30px;
        font-size: 1.2rem;
        width: 100%;
    }
    
    .admin-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }
    
    @media (max-width: 768px) {
        .product-detail {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
const maxStock = {{ $product->stock }};

function increaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue < maxStock) {
        input.value = currentValue + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}

async function addToCartWithQuantity() {
    const quantity = parseInt(document.getElementById('quantity').value);
    
    try {
        const response = await fetchAPI('/cart/add', {
            method: 'POST',
            body: JSON.stringify({
                product_id: {{ $product->id }},
                quantity: quantity
            })
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            updateCartBadge();
        }
    } catch (error) {
        showToast(error.message || 'Failed to add to cart', 'error');
    }
}

async function deleteProduct(productId) {
    if (!confirmDelete('Are you sure you want to delete this product?')) {
        return;
    }
    
    try {
        const response = await fetchAPI(`/products/${productId}`, {
            method: 'DELETE'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => {
                window.location.href = '{{ route("products.index") }}';
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete product', 'error');
    }
}
</script>
@endpush