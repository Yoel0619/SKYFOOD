<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FoodHub - Order Food Online')</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="{{ route('home') }}">
                    <h1>🍔 SKYFOOD💫</h1>
                </a>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('menu.index') }}">Menu</a></li>
                
                @auth
                    <li><a href="{{ route('cart.index') }}">
                        Cart <span id="cartCount" class="cart-badge">0</span>
                    </a></li>
                    <li><a href="{{ route('orders.index') }}">My Orders</a></li>
                    
                    @if(Auth::user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @endif
                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">
                            {{ Auth::user()->name }} ▼
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="event.preventDefault(); logout();">Logout</a></li>
                        </ul>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
            <button class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>FoodHub</h3>
                    <p>Delicious food delivered to your door</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="{{ route('menu.index') }}">Menu</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>📞 +255 712 345 678</p>
                    <p>📧 info@foodhub.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 FoodHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
    
    <script>
        // Logout function
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                fetch('{{ route('logout') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '{{ route('home') }}';
                    }
                });
            }
        }

        // Mobile menu toggle
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        
        if (hamburger) {
            hamburger.addEventListener('click', () => {
                navMenu.classList.toggle('active');
            });
        }
    </script>
</body>
</html>