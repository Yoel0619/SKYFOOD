@extends('layouts.app')

@section('title', 'Home - Food Ordering System')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Delicious Food Delivered to Your Door</h1>
                <p class="lead mb-4">Order your favorite meals from the best restaurants in town. Fast delivery guaranteed!</p>
                <a href="{{ route('menu.index') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-book-open"></i> View Menu
                </a>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-hamburger fa-10x opacity-50"></i>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Browse Categories</h2>
        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-md-4 col-lg-2">
                <a href="{{ route('menu.index', ['category' => $category->id]) }}" class="text-decoration-none">
                    <div class="card h-100 category-card">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" class="card-img-top" alt="{{ $category->name }}">
                        @else
                            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="fas fa-utensils fa-3x text-white"></i>
                            </div>
                        @endif
                        <div class="card-body text-center">
                            <h6 class="card-title mb-0">{{ $category->name }}</h6>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Foods Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Featured Foods</h2>
        <div class="row g-4">
            @foreach($featuredFoods as $food)
            <div class="col-md-6 col-lg-3">
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
                    
                    <div class="card-body">
                        <h6 class="card-title">{{ $food->name }}</h6>
                        <p class="text-muted small mb-2">{{ $food->category->name }}</p>
                        <p class="card-text small">{{ Str::limit($food->description, 60) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($food->discount_percentage > 0)
                                    <span class="text-decoration-line-through text-muted">TSh {{ number_format($food->price, 0) }}</span>
                                    <span class="fw-bold text-primary d-block">TSh {{ number_format($food->final_price, 0) }}</span>
                                @else
                                    <span class="fw-bold text-primary">TSh {{ number_format($food->price, 0) }}</span>
                                @endif
                            </div>
                            <div>
                                @if($food->reviews_count > 0)
                                    <small class="text-warning">
                                        <i class="fas fa-star"></i> {{ number_format($food->average_rating, 1) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('menu.show', $food->slug) }}" class="btn btn-outline-primary btn-sm w-100">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('menu.index') }}" class="btn btn-primary">
                View All Menu <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                </div>
                <h5>Fast Delivery</h5>
                <p class="text-muted">Get your food delivered within 45 minutes</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-icon">
                    <i class="fas fa-utensils fa-3x text-primary mb-3"></i>
                </div>
                <h5>Quality Food</h5>
                <p class="text-muted">Fresh ingredients and delicious recipes</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-icon">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                </div>
                <h5>24/7 Support</h5>
                <p class="text-muted">We're here to help anytime you need</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Update cart count on page load
    updateCartCount();
</script>
@endpush