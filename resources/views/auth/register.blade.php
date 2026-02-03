@extends('layouts.app')

@section('title', 'Register - FoodHub')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Create Account</h2>
            <p>Join FoodHub today!</p>
        </div>
        
        <form id="registerForm" class="auth-form">
            @csrf
            
            <div id="errorMessage" class="alert alert-error" style="display: none;"></div>
            <div id="successMessage" class="alert alert-success" style="display: none;"></div>
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
                <span class="error-text" id="name-error"></span>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
                <span class="error-text" id="email-error"></span>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+255...">
                <span class="error-text" id="phone-error"></span>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <span class="error-text" id="password-error"></span>
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                <span class="error-text" id="password_confirmation-error"></span>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block" id="registerBtn">
                <span id="registerBtnText">Register</span>
                <span id="registerBtnLoader" class="btn-loader" style="display: none;">
                    <span class="spinner"></span> Creating account...
                </span>
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const registerBtn = document.getElementById('registerBtn');
    const registerBtnText = document.getElementById('registerBtnText');
    const registerBtnLoader = document.getElementById('registerBtnLoader');
    const errorMessage = document.getElementById('errorMessage');
    const successMessage = document.getElementById('successMessage');
    
    // Clear previous errors
    document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
    errorMessage.style.display = 'none';
    successMessage.style.display = 'none';
    
    // Disable button
    registerBtn.disabled = true;
    registerBtnText.style.display = 'none';
    registerBtnLoader.style.display = 'inline-block';
    
    const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value
    };
    
    try {
        const response = await fetch('{{ route('register') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            successMessage.textContent = data.message;
            successMessage.style.display = 'block';
            
            // Redirect to login after 2 seconds
            setTimeout(() => {
                window.location.href = '{{ route('login') }}';
            }, 2000);
        } else {
            if (data.errors) {
                // Display field-specific errors
                Object.keys(data.errors).forEach(field => {
                    const errorEl = document.getElementById(`${field}-error`);
                    if (errorEl) {
                        errorEl.textContent = data.errors[field][0];
                    }
                });
            }
            
            errorMessage.textContent = 'Please fix the errors below.';
            errorMessage.style.display = 'block';
            
            // Re-enable button
            registerBtn.disabled = false;
            registerBtnText.style.display = 'inline-block';
            registerBtnLoader.style.display = 'none';
        }
    } catch (error) {
        errorMessage.textContent = 'An error occurred. Please try again.';
        errorMessage.style.display = 'block';
        
        // Re-enable button
        registerBtn.disabled = false;
        registerBtnText.style.display = 'inline-block';
        registerBtnLoader.style.display = 'none';
    }
});
</script>
@endpush