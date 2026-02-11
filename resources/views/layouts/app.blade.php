<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Food Ordering System')</title>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Inline CSS for Form Animation and Styling -->
    <style>
    /* Reset and Base Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      height: 100vh;
      background: url(422b08fa7c3975d0ed4870ae8f7710f5.jpg);
      backdrop-filter: blur(20px);
      display: flex;
      justify-content: center;
      align-items: center;
      perspective: 1000px; /* 👈 Enables 3D transform */
      overflow: hidden;
    }

    /* 📖 Animated Form Container */
    .form-container {
      background-color: white;
      padding: 40px 30px;
      border-radius: 12px;
      width: 350px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
      transform-style: preserve-3d;
      transform-origin: left center; /* Open from left like a book */
      animation: bookOpen 1s ease-out forwards;
      opacity: 0;
      transform: rotateY(-90deg); /* Start closed */
      text-shadow: greenyellow;
    }

    /* Heading */
    .form-container h2 {
      text-align: center;
      color: #333;
      margin-bottom: 25px;
      text-shadow: orange;
    }

    /* 📝 Input Fields */
    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    /* ✨ Input Focus Animation */
    input:focus {
      border-color: #667eea;
      box-shadow: 0 0 8px rgba(102, 126, 234, 0.5);
      outline: none;
    }

    /* 🚀 Button Styles */
    button[type="submit"] {
      width: 100%;
      padding: 12px;
      background-color: #667eea;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      transform: scale(1);
    }

    /* 🧲 Button Hover Animation */
    button[type="submit"]:hover {
      background-color: #5a67d8;
      transform: scale(1.05);
    }

    /* 📚 Book Open Animation */
    @keyframes bookOpen {
      0% {
        opacity: 0;
        transform: rotateY(-360deg);
      }
      100% {
        opacity: 1;
        transform: rotateY(0deg);
      }
    }

    .update-btn {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-left: 8px;
    }

    .update-btn:hover {
      background-color: #0056b3;
      transform: scale(1.05);
    }
    </style>

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
                    <a href="{{ route('payments.index') }}">Payments</a>
                    <a href="{{ route('deliveries.index') }}">Deliveries</a>
                    <a href="{{ route('users.index') }}">Users</a>
                    <a href="{{ route('roles.index') }}">Roles</a>
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
    <script src="{{ asset('js/dark-mode.js') }}"></script>

    @stack('scripts')
</body>
</html>
