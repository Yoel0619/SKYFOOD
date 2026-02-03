@extends('layouts.app')

@section('title', 'SKYFOOD - Delicious Food Delivered')

@section('content')
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Delicious Food, Delivered Fast 🚀</h1>
            <p class="hero-subtitle">Order your favorite meals from the best restaurants in town</p>
            <div class="hero-buttons">
                <a href="{{ route('menu.index') }}" class="btn btn-primary btn-lg">Order Now</a>
                <a href="#features" class="btn btn-outline btn-lg">Learn More</a>
            </div>
        </div>
    </div>
</section>

<section id="features" class="features-section">
    <div class="container">
        <h2 class="section-title">Why Choose Skyfood?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🍕</div>
                <h3>Wide Selection</h3>
                <p>Choose from hundreds of delicious dishes from top restaurants</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Fast Delivery</h3>
                <p>Get your food delivered hot and fresh in 30 minutes or less</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <h3>Easy Payment</h3>
                <p>Multiple payment options including cash, card, and mobile money</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>Track Orders</h3>
                <p>Real-time order tracking from kitchen to your doorstep</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3>Quality Food</h3>
                <p>Only the best quality ingredients from trusted restaurants</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Great Deals</h3>
                <p>Special offers and discounts on your favorite meals</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Order?</h2>
            <p>Join thousands of satisfied customers who trust FoodHub for their meals</p>
            <a href="{{ route('menu.index') }}" class="btn btn-primary btn-lg">Browse Menu</a>
        </div>
    </div>
</section>
@endsection