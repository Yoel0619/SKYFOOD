<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SkyFood')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main CSS - MASTER FILE -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Page Specific CSS -->
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <span class="brand-icon">🍕</span>
                <span>SKYFOOD</span>
            </a>
            
            <ul class="nav-menu">
                <li><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
                
                @if(auth()->user()->role->name === 'admin')
                    <li><a href="{{ route('products.index') }}"><i class="fas fa-utensils"></i> Menu</a></li>
                    <li><a href="{{ route('categories.index') }}"><i class="fas fa-list"></i> Categories</a></li>
                    <li><a href="{{ route('orders.index') }}"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="{{ route('payments.index') }}"><i class="fas fa-money-bill"></i> Payments</a></li>
                    <li><a href="{{ route('deliveries.index') }}"><i class="fas fa-truck"></i> Deliveries</a></li>
                    <li><a href="{{ route('users.index') }}"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="{{ route('roles.index') }}"><i class="fas fa-user-tag"></i> Roles</a></li>
                @else
                    <li><a href="{{ route('products.index') }}"><i class="fas fa-utensils"></i> Menu</a></li>
                    <li><a href="{{ route('cart.index') }}" class="cart-link">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <span class="cart-badge">0</span>
                    </a></li>
                    <li><a href="{{ route('orders.index') }}"><i class="fas fa-receipt"></i> My Orders</a></li>
                @endif
                
                <li class="user-menu">
                    <div class="user-dropdown">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>