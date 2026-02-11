<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Food Ordering System</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>SKYFOOD🌃💫</h1>
                <p>Create your account to get started</p>
            </div>
            
            <form id="registerForm" action="{{ route('register.post') }}" method="POST">
                @csrf
                
                <!-- Role Selection -->
                <div class="form-group">
                    <label for="role">Register As *</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="">Select Account Type</option>
                        <option value="customer" selected>Customer (Order Food)</option>
                        <option value="admin">Admin (Manage System)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="name" 
                        name="name" 
                        placeholder="John Doe"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
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
                    <label for="phone">Phone Number *</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="phone" 
                        name="phone" 
                        placeholder="0712345678"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="address">Delivery Address *</label>
                    <textarea 
                        class="form-control" 
                        id="address" 
                        name="address" 
                        rows="3" 
                        placeholder="Enter your full delivery address"
                        required
                    ></textarea>
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="Minimum 8 characters"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password *</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="Re-enter your password"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
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