@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="container">
    <h1 class="mb-4">Our Menu</h1>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('menu.index') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" placeholder="Search food..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Min Price</label>
                        <input type="number" class="form-control" name="min_price" placeholder="Min" value="{{ request('min_price') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Max Price</label>
                        <input type="number" class="form-control" name="max_price" placeholder="Max" value="{{ request('max_price') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Sort By</label>
                        <select class="form-select" name="sort">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        </select>
                    </div>
                    
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="vegetarian" value="1" id="vegetarian" {{ request('vegetarian') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="vegetarian">
                            Vegetarian Only
                        </label>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Food Items -->
    @if($foods->count() > 0)
        <div class="row g-4">
            @foreach($foods as $food)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 food-card">
                    @if($food->image)
                        <img src="{{ asset('storage/' . $food->image) }}" class="card-img-top" alt="{{ $food->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-white"></i>
                        </div>
                    @endif
                    
                    @if($food->discount_percentage > 0)
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                            -{{ $food->discount_percentage }}%
                        </span>
                    @endif
                    
                    @if($food->is_vegetarian)
                        <span class="badge bg-success position-absolute top-0 start-0 m-2">
                            <i class="fas fa-leaf"></i> Veg
                        </span>
                    @endif
                    
                    <div class="card-body">
                        <h6 class="card-title">{{ $food->name }}</h6>
                        <p class="text-muted small mb-1">
                            <i class="fas fa-tag"></i> {{ $food->category->name }}
                        </p>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-clock"></i> {{ $food->preparation_time }} mins
                            @if($food->calories)
                                | <i class="fas fa-fire"></i> {{ $food->calories }} cal
                            @endif
                        </p>
                        <p class="card-text small">{{ Str::limit($food->description, 80) }}</p>
                        
                        <div class="mb-2">
                            @if($food->discount_percentage > 0)
                                <span class="text-decoration-line-through text-muted small">TSh {{ number_format($food->price, 0) }}</span>
                                <span class="fw-bold text-primary d-block">TSh {{ number_format($food->final_price, 0) }}</span>
                            @else
                                <span class="fw-bold text-primary">TSh {{ number_format($food->price, 0) }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            @auth
                                <button class="btn btn-primary btn-sm add-to-cart" data-food-id="{{ $food->id }}">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sign-in-alt"></i> Login to Order
                                </a>
                            @endauth
                            <a href="{{ route('menu.show', $food->slug) }}" class="btn btn-outline-secondary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-4 d-flex justify-content-center">
            {{ $foods->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> No food items found matching your criteria.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/menu.js') }}"></script>
@endpush