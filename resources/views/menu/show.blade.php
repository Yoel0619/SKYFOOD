@extends('layouts.app')

@section('title', $food->name)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
            <li class="breadcrumb-item active">{{ $food->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-6 mb-4">
            @if($food->image)
                <img src="{{ asset('storage/' . $food->image) }}" class="img-fluid rounded shadow" alt="{{ $food->name }}">
            @else
                <div class="bg-secondary d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                    <i class="fas fa-image fa-5x text-white"></i>
                </div>
            @endif
        </div>
        
        <div class="col-lg-6">
            <h1>{{ $food->name }}</h1>
            
            <div class="mb-3">
                <span class="badge bg-primary">{{ $food->category->name }}</span>
                @if($food->is_vegetarian)
                    <span class="badge bg-success"><i class="fas fa-leaf"></i> Vegetarian</span>
                @endif
                @if(!$food->is_available)
                    <span class="badge bg-danger">Currently Unavailable</span>
                @endif
            </div>
            
            <div class="mb-3">
                @if($food->review_count > 0)
                    <span class="text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($food->average_rating))
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </span>
                    <span class="text-muted">({{ $food->review_count }} reviews)</span>
                @else
                    <span class="text-muted">No reviews yet</span>
                @endif
            </div>
            
            <div class="mb-3">
                @if($food->discount_percentage > 0)
                    <h3 class="text-muted text-decoration-line-through">TSh {{ number_format($food->price, 0) }}</h3>
                    <h2 class="text-primary">TSh {{ number_format($food->final_price, 0) }}</h2>
                    <span class="badge bg-danger">Save {{ $food->discount_percentage }}%</span>
                @else
                    <h2 class="text-primary">TSh {{ number_format($food->price, 0) }}</h2>
                @endif
            </div>
            
            <p class="lead">{{ $food->description }}</p>
            
            <div class="mb-3">
                <h5>Details:</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-clock text-primary"></i> Preparation Time: {{ $food->preparation_time }} minutes</li>
                    @if($food->calories)
                        <li><i class="fas fa-fire text-danger"></i> Calories: {{ $food->calories }} cal</li>
                    @endif
                </ul>
            </div>
            
            @if($food->ingredients)
                <div class="mb-3">
                    <h5>Ingredients:</h5>
                    <p>{{ $food->ingredients }}</p>
                </div>
            @endif
            
            @if($food->allergen_info)
                <div class="mb-3">
                    <h5>Allergen Information:</h5>
                    <p class="text-danger">{{ $food->allergen_info }}</p>
                </div>
            @endif
            
            @if($food->is_available)
                @auth
                    <div class="mb-3">
                        <label class="form-label">Quantity:</label>
                        <div class="input-group" style="max-width: 150px;">
                            <button class="btn btn-outline-secondary" type="button" id="decreaseQty">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="10">
                            <button class="btn btn-outline-secondary" type="button" id="increaseQty">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary btn-lg add-to-cart" data-food-id="{{ $food->id }}">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Login to Order
                    </a>
                @endauth
            @else
                <button class="btn btn-secondary btn-lg" disabled>
                    Currently Unavailable
                </button>
            @endif
        </div>
    </div>
    
    <!-- Reviews Section -->
    @if($food->reviews->count() > 0)
        <div class="mt-5">
            <h3>Customer Reviews</h3>
            <div class="row">
                @foreach($food->reviews as $review)
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    @if($review->user->profile_image)
                                        <img src="{{ asset('storage/' . $review->user->profile_image) }}" alt="{{ $review->user->name }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <i class="fas fa-user-circle fa-2x me-2"></i>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $review->user->name }}</h6>
                                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                                @if($review->review_text)
                                    <p class="mb-0">{{ $review->review_text }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Related Foods -->
    @if($relatedFoods->count() > 0)
        <div class="mt-5">
            <h3>You May Also Like</h3>
            <div class="row g-3">
                @foreach($relatedFoods as $relatedFood)
                    <div class="col-md-3">
                        <div class="card h-100">
                            @if($relatedFood->image)
                                <img src="{{ asset('storage/' . $relatedFood->image) }}" class="card-img-top" alt="{{ $relatedFood->name }}" style="height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 150px;">
                                    <i class="fas fa-image fa-2x text-white"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <h6 class="card-title">{{ $relatedFood->name }}</h6>
                                <p class="fw-bold text-primary mb-2">TSh {{ number_format($relatedFood->final_price, 0) }}</p>
                                <a href="{{ route('menu.show', $relatedFood->slug) }}" class="btn btn-outline-primary btn-sm w-100">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/menu.js') }}"></script>
<script>
    // Quantity controls
    document.getElementById('increaseQty').addEventListener('click', function() {
        let qty = document.getElementById('quantity');
        if (parseInt(qty.value) < 10) {
            qty.value = parseInt(qty.value) + 1;
        }
    });
    
    document.getElementById('decreaseQty').addEventListener('click', function() {
        let qty = document.getElementById('quantity');
        if (parseInt(qty.value) > 1) {
            qty.value = parseInt(qty.value) - 1;
        }
    });
</script>
@endpush