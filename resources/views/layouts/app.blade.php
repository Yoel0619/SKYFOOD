<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Food Ordering System')</title>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    @auth
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="{{ route('dashboard') }}">SKYFOOD🌃💫</a>
            </div>
            
            <div class="nav-menu" id="navMenu">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('products.index') }}">Menu</a>
                
                @if(auth()->user()->isCustomer())
                    <a href="{{ route('cart.index') }}">
                        Cart <span class="cart-badge" id="cartBadge">0</span>
                    </a>
                    <a href="{{ route('orders.index') }}">My Orders</a>
                @endif
                
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('categories.index') }}">Categories</a>
                    <a href="{{ route('orders.index') }}">Orders</a>
                    <a href="{{ route('users.index') }}">Users</a>
                @endif
                
                <div class="nav-dropdown">
                    <a href="#" class="dropdown-toggle">
                        {{ auth()->user()->name }} ▼
                    </a>
                    <div class="dropdown-menu">
                        <a href="{{ route('profile') }}">Profile</a>
                        <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                                Logout
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
    @endauth
    
    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>
    
    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>
    
    <!-- Loading Spinner -->
    <div class="spinner-overlay" id="spinnerOverlay">
        <div class="spinner"></div>
    </div>
    
    <!-- Custom JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    @stack('scripts')
</body>
</html>