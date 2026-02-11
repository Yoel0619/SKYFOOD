@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-utensils"></i> Our Products</h1>
            <p>Browse our delicious food and drinks</p>
        </div>
        @if(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Product
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
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <button class="btn btn-outline" onclick="clearFilters()">
            <i class="fas fa-times"></i> Clear
        </button>
    </div>
    
    <!-- Category Cards (Visual Selection) -->
    @if($categories->count() > 0)
    <div class="category-cards-container">
        <h3 style="margin-bottom: 1rem; color: #454545;">
            <i class="fas fa-utensils"></i> Browse by Category
        </h3>
        <div class="category-cards">
            <div class="category-card {{ !request('category') ? 'active' : '' }}" onclick="selectCategory('')">
                <div class="category-icon">🍽️</div>
                <h4>All Items</h4>
                <p>{{ $products->total() }} items</p>
            </div>
            
            @foreach($categories as $category)
            <div class="category-card {{ request('category') == $category->id ? 'active' : '' }}" 
                 onclick="selectCategory({{ $category->id }})">
                <div class="category-icon">
                    @if($category->image)
                        <img src="{{ $category->image }}" alt="{{ $category->name }}">
                    @else
                        🍴
                    @endif
                </div>
                <h4>{{ $category->name }}</h4>
                <p>{{ $category->products->count() }} items</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Products Grid -->
    <div class="products-grid">
        @forelse($products as $product)
            <div class="product-card">
                <div class="product-image">
                    @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}">
                    @else
                        <div class="placeholder-image">
                            <i class="fas fa-utensils"></i>
                        </div>
                    @endif
                    
                    @if($product->status == 'unavailable')
                        <div class="out-of-stock-badge">Out of Stock</div>
                    @endif
                </div>
                
                <div class="product-info">
                    <span class="product-category">{{ $product->category->name }}</span>
                    <h3>{{ $product->name }}</h3>
                    <p>{{ Str::limit($product->description, 80) }}</p>
                    
                    <div class="product-footer">
                        <div class="product-price">
                            <strong>TZS {{ number_format($product->price, 0) }}</strong>
                        </div>
                        
                        <div class="product-actions">
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button 
                                    class="btn btn-sm btn-danger" 
                                    onclick="deleteItem('/products/{{ $product->id }}', 'Delete {{ $product->name }}?')"
                                >
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            @else
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                @if($product->status == 'available' && $product->stock > 0)
                                    <button 
                                        class="btn btn-sm btn-success" 
                                        onclick="addToCart({{ $product->id }})"
                                    >
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled>
                                        <i class="fas fa-ban"></i> Unavailable
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    <div class="product-stock">
                        <small>Stock: {{ $product->stock }} units</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div style="font-size: 80px; margin-bottom: 1rem;">🍽️</div>
                <h3>No Products Found</h3>
                <p>Try adjusting your search or filter criteria</p>
                @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Product
                    </a>
                @endif
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($products->hasPages())
    <div class="pagination-container">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.category-cards-container {
    margin-bottom: 2rem;
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, .08);
}

.category-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1.5rem;
}

.category-card {
    background: linear-gradient(135deg, rgba(251, 175, 50, 0.05), rgba(113, 154, 10, 0.05));
    padding: 1.5rem;
    border-radius: 15px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    border: 3px solid transparent;
}

.category-card:hover {
    transform: translateY(-5px);
    border-color: #fbaf32;
    box-shadow: 0 8px 20px rgba(251, 175, 50, 0.3);
}

.category-card.active {
    background: linear-gradient(135deg, #fbaf32, #719a0a);
    color: white;
    border-color: #fbaf32;
}

.category-icon {
    font-size: 48px;
    margin-bottom: 1rem;
}

.category-icon img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.category-card h4 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.category-card.active h4,
.category-card.active p {
    color: white;
}

.category-card p {
    font-size: 14px;
    color: #757575;
    margin: 0;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0, 0, 0, .08);
    transition: all 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
}

.product-image {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: #f8f9fa;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.placeholder-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 60px;
    color: #ddd;
}

.out-of-stock-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #d63031;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
}

.product-info {
    padding: 1.5rem;
}

.product-category {
    display: inline-block;
    background: linear-gradient(135deg, #fbaf32, #719a0a);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.product-info h3 {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0.5rem 0;
    color: #454545;
}

.product-info p {
    color: #757575;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
}

.product-price strong {
    font-size: 1.3rem;
    color: #fbaf32;
}

.product-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.product-stock {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #f0f0f0;
}

.product-stock small {
    color: #757575;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state h3 {
    color: #454545;
    margin-bottom: 1rem;
}

.empty-state p {
    color: #757575;
    margin-bottom: 2rem;
}
</style>
@endpush

@push('scripts')
<script>
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

function clearFilters() {
    window.location.href = '{{ route("products.index") }}';
}

function selectCategory(categoryId) {
    const params = new URLSearchParams(window.location.search);
    
    if (categoryId) {
        params.set('category', categoryId);
    } else {
        params.delete('category');
    }
    
    window.location.href = '{{ route("products.index") }}?' + params.toString();
}

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
</script>
@endpush