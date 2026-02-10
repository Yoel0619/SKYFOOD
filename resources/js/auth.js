// Auth page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Login Form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                showSpinner();
                
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                hideSpinner();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    
                    // Redirect to dashboard
                    setTimeout(() => {
                        window.location.href = result.redirect || '/dashboard';
                    }, 500);
                } else {
                    // Show errors
                    if (result.errors) {
                        displayFormErrors('loginForm', result.errors);
                    } else if (result.message) {
                        showToast(result.message, 'error');
                    }
                }
            } catch (error) {
                hideSpinner();
                showToast('Login failed. Please try again.', 'error');
                console.error('Login error:', error);
            }
        });
    }
    
    // Register Form
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Password confirmation check
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            
            if (password !== passwordConfirmation) {
                showToast('Passwords do not match!', 'error');
                return;
            }
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                showSpinner();
                
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                hideSpinner();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    
                    // Redirect to dashboard
                    setTimeout(() => {
                        window.location.href = result.redirect || '/dashboard';
                    }, 500);
                } else {
                    // Show errors
                    if (result.errors) {
                        displayFormErrors('registerForm', result.errors);
                    } else if (result.message) {
                        showToast(result.message, 'error');
                    }
                }
            } catch (error) {
                hideSpinner();
                showToast('Registration failed. Please try again.', 'error');
                console.error('Register error:', error);
            }
        });
    }
    
    // Demo credentials click
    const demoCredentials = document.querySelectorAll('.demo-credentials p');
    demoCredentials.forEach(p => {
        p.addEventListener('click', function() {
            const text = this.textContent;
            
            if (text.includes('admin@foodorder.com')) {
                document.getElementById('email').value = 'admin@foodorder.com';
                document.getElementById('password').value = 'password123';
            } else if (text.includes('customer@example.com')) {
                document.getElementById('email').value = 'customer@example.com';
                document.getElementById('password').value = 'password123';
            }
            
            showToast('Demo credentials filled!', 'success');
        });
    });
});

// Helper functions
function showSpinner() {
    const spinner = document.getElementById('spinnerOverlay');
    if (spinner) {
        spinner.classList.add('active');
    }
}

function hideSpinner() {
    const spinner = document.getElementById('spinnerOverlay');
    if (spinner) {
        spinner.classList.remove('active');
    }
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.textContent = message;
        toast.className = `toast ${type} show`;
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
}

function displayFormErrors(formId, errors) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    // Clear previous errors
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    form.querySelectorAll('.form-control').forEach(el => el.classList.remove('error'));
    
    // Display new errors
    Object.keys(errors).forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('error');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = errors[field][0];
            input.parentNode.appendChild(errorDiv);
        }
    });
    
    // Show first error in toast
    const firstError = Object.values(errors)[0][0];
    showToast(firstError, 'error');
}