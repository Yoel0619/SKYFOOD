@extends('layouts.app')

@section('title', 'Menu - Products')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Our Menu</h1>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                ➕ Add Product
            </a>
        @endif
    </div>
    
    <!-- Search & Filter Bar -->
    <div class="filter-bar">
        <input 
            type="text" 
            class="form-control" 
            id="searchInput" 
            placeholder="🔍 Search products..."
            value="{{ request('search') }}"
        >
        
        <select class="form-control" id="categoryFilter">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        
        <select class="form-control" id="statusFilter">
            <option value="">All Status</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
            <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
        </select>
        
        <button class="btn btn-secondary" onclick="applyFilters()">
            Filter
        </button>
        
        <button class="btn btn-outline" onclick="clearFilters()">
            Clear
        </button>
    </div>
    
    <!-- Products Grid -->
    <div class="products-grid">
        @forelse($products as $product)
        <div class="product-card">
            <div class="product-image">
                @if($product->image)
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                @else
                    <img src="https://via.placeholder.com/300x200?text={{ urlencode($product->name) }}" alt="{{ $product->name }}">
                @endif
                
                @if($product->status == 'unavailable')
                    <div class="product-badge unavailable">Unavailable</div>
                @endif
                
                @if($product->stock < 10 && $product->stock > 0)
                    <div class="product-badge low-stock">Low Stock</div>
                @endif
                
                @if($product->stock == 0)
                    <div class="product-badge out-of-stock">Out of Stock</div>
                @endif
            </div>
            
            <div class="product-info">
                <h3>{{ $product->name }}</h3>
                <p class="product-category">{{ $product->category->name }}</p>
                <p class="product-description">{{ Str::limit($product->description, 80) }}</p>
                
                <div class="product-footer">
                    <div class="product-price">
                        <span class="price">TZS {{ number_format($product->price, 0) }}</span>
                        <span class="stock">Stock: {{ $product->stock }}</span>
                    </div>
                    
                    <div class="product-actions">
                        @if(auth()->user()->isCustomer())
                            @if($product->status == 'available' && $product->stock > 0)
                                <button 
                                    class="btn btn-sm btn-primary" 
                                    onclick="addToCart({{ $product->id }})"
                                >
                                    🛒 Add to Cart
                                </button>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>
                                    Unavailable
                                </button>
                            @endif
                        @endif
                        
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline">
                            View Details
                        </a>
                        
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                ✏️ Edit
                            </a>
                            <button 
                                class="btn btn-sm btn-danger" 
                                onclick="deleteProduct({{ $product->id }})"
                            >
                                🗑️ Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <h3>No products found</h3>
            <p>Try adjusting your filters or search terms</p>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    Add First Product
                </a>
            @endif
        </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="pagination-container">
        {{ $products->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    
    .product-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
    }
    
    .product-badge.unavailable {
        background: var(--danger-color);
    }
    
    .product-badge.low-stock {
        background: var(--warning-color);
        color: var(--dark-color);
    }
    
    .product-badge.out-of-stock {
        background: var(--danger-color);
    }
    
    .product-info {
        padding: 1.5rem;
    }
    
    .product-info h3 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        color: var(--dark-color);
    }
    
    .product-category {
        color: var(--primary-color);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .product-description {
        color: #636e72;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    
    .product-footer {
        border-top: 1px solid var(--border-color);
        padding-top: 1rem;
    }
    
    .product-price {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .product-price .price {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    
    .product-price .stock {
        font-size: 0.875rem;
        color: #636e72;
    }
    
    .product-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: #636e72;
        margin-bottom: 1.5rem;
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script>
// Add to Cart
async function addToCart(productId) {
    try {
        const response = await fetchAPI('/cart/add', {
            method: 'POST',
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
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

// Delete Product (Admin)
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
                location.reload();
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete product', 'error');
    }
}

// Apply Filters
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (category) params.append('category', category);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("products.index") }}?' + params.toString();
}

// Clear Filters
function clearFilters() {
    window.location.href = '{{ route("products.index") }}';
}

// Enter key to search
document.getElementById('searchInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        applyFilters();
    }
});
</script>
@endpush