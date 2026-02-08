@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container">
    <div class="page-header">
        <h1>{{ $category->name }}</h1>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('categories.index') }}" class="btn btn-outline">
                ← Back to Categories
            </a>
            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">
                ✏️ Edit
            </a>
        </div>
    </div>
    
    <div class="category-detail">
        <div class="card">
            <div class="card-header">
                <h2>Category Information</h2>
                @if($category->status == 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">Inactive</span>
                @endif
            </div>
            
            @if($category->image)
            <div class="category-image-large">
                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}">
            </div>
            @endif
            
            <div class="category-info">
                <div class="info-row">
                    <label>Name:</label>
                    <strong>{{ $category->name }}</strong>
                </div>
                
                <div class="info-row">
                    <label>Description:</label>
                    <p>{{ $category->description ?? 'No description' }}</p>
                </div>
                
                <div class="info-row">
                    <label>Total Products:</label>
                    <strong>{{ $category->products->count() }}</strong>
                </div>
                
                <div class="info-row">
                    <label>Created:</label>
                    <strong>{{ $category->created_at->format('M d, Y H:i') }}</strong>
                </div>
                
                <div class="info-row">
                    <label>Last Updated:</label>
                    <strong>{{ $category->updated_at->format('M d, Y H:i') }}</strong>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Products in this Category ({{ $category->products->count() }})</h2>
            </div>
            
            @if($category->products->count() > 0)
                <div class="products-grid">
                    @foreach($category->products as $product)
                    <div class="product-card-small">
                        <div class="product-image-small">
                            @if($product->image)
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                            @else
                                <img src="https://via.placeholder.com/150?text={{ urlencode($product->name) }}" alt="{{ $product->name }}">
                            @endif
                        </div>
                        <div class="product-info-small">
                            <h4>{{ $product->name }}</h4>
                            <p class="price">TZS {{ number_format($product->price, 0) }}</p>
                            <p class="stock">Stock: {{ $product->stock }}</p>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-primary">
                                View Product
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>No products in this category yet</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        Add Product
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .category-detail {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .category-image-large {
        width: 100%;
        max-height: 300px;
        overflow: hidden;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    
    .category-image-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .category-info {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .info-row {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .info-row label {
        font-size: 0.875rem;
        color: #636e72;
    }
    
    .info-row strong,
    .info-row p {
        font-size: 1rem;
        color: var(--dark-color);
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .product-card-small {
        background: var(--light-color);
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s;
    }
    
    .product-card-small:hover {
        transform: translateY(-3px);
    }
    
    .product-image-small {
        height: 150px;
        overflow: hidden;
    }
    
    .product-image-small img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-info-small {
        padding: 1rem;
    }
    
    .product-info-small h4 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .product-info-small .price {
        color: var(--primary-color);
        font-weight: bold;
        margin-bottom: 0.25rem;
    }
    
    .product-info-small .stock {
        font-size: 0.875rem;
        color: #636e72;
        margin-bottom: 0.5rem;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
    }
</style>
@endpush