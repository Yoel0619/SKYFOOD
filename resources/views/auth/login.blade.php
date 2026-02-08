<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Food Ordering System</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>SKYFOOD🌃💫</h1>
                <p>Welcome back! Please login to your account</p>
            </div>
            
            <form id="loginForm" action="{{ route('login.post') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        placeholder="your@email.com"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Login
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
            </div>
            
            <div class="demo-credentials">
                <h4>Demo Credentials:</h4>
                <p><strong>Admin:</strong> admin@foodorder.com / password123</p>
                <p><strong>Customer:</strong> customer@example.com / password123</p>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>
    
    <!-- Loading Spinner -->
    <div class="spinner-overlay" id="spinnerOverlay">
        <div class="spinner"></div>
    </div>
    
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>