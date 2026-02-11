// Login Form Handler
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        try {
            showSpinner();
            
            const response = await fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            hideSpinner();
            
            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 800);
            } else {
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const input = document.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('error');
                            const errorDiv = document.createElement('span');
                            errorDiv.className = 'error-message';
                            errorDiv.textContent = result.errors[field][0];
                            input.parentNode.appendChild(errorDiv);
                        }
                    });
                } else {
                    showToast(result.message || 'Login failed', 'error');
                }
            }
        } catch (error) {
            hideSpinner();
            showToast('An error occurred. Please try again.', 'error');
            console.error('Login error:', error);
        }
    });
}

// Register Form Handler
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear previous errors
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        try {
            showSpinner();
            
            const response = await fetch('/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            hideSpinner();
            
            if (result.success) {
                showToast(result.message, 'success');
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 800);
            } else {
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const input = document.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('error');
                            const errorDiv = document.createElement('span');
                            errorDiv.className = 'error-message';
                            errorDiv.textContent = result.errors[field][0];
                            input.parentNode.appendChild(errorDiv);
                        }
                    });
                    showToast('Please fix the errors', 'error');
                } else {
                    showToast(result.message || 'Registration failed', 'error');
                }
            }
        } catch (error) {
            hideSpinner();
            showToast('An error occurred. Please try again.', 'error');
            console.error('Register error:', error);
        }
    });
}

// Demo Credentials Click-to-Fill
document.querySelectorAll('.demo-item').forEach(item => {
    item.addEventListener('click', function() {
        const email = this.getAttribute('data-email');
        const password = this.getAttribute('data-password');
        
        document.querySelector('[name="email"]').value = email;
        document.querySelector('[name="password"]').value = password;
        
        showToast('Credentials filled!', 'success');
    });
});

// Show Toast
function showToast(message, type = 'success') {
    let toast = document.getElementById('toast');
    
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    
    toast.textContent = message;
    toast.className = `toast ${type} show`;
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Show/Hide Spinner
function showSpinner() {
    let spinner = document.getElementById('spinnerOverlay');
    if (!spinner) {
        spinner = document.createElement('div');
        spinner.id = 'spinnerOverlay';
        spinner.className = 'spinner-overlay';
        spinner.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(spinner);
    }
    spinner.classList.add('active');
}

function hideSpinner() {
    const spinner = document.getElementById('spinnerOverlay');
    if (spinner) {
        spinner.classList.remove('active');
    }
}