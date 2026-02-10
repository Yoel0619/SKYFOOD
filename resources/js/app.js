// CSRF Token Setup
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Fetch API Helper
async function fetchAPI(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    };

    const mergedOptions = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...options.headers,
        },
    };

    const response = await fetch(url, mergedOptions);
    
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.message || 'Request failed');
    }
    
    return await response.json();
}

// Toast Notification
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

// Handle Form Submit
async function handleFormSubmit(formId, callback) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = this.action;
        const method = this.method.toUpperCase();
        
        // Check if form has file uploads
        const hasFiles = Array.from(formData.entries()).some(([key, value]) => value instanceof File && value.size > 0);
        
        try {
            showSpinner();
            
            let data;
            
            if (hasFiles) {
                // For file uploads, use FormData
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                
                data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Request failed');
                }
            } else {
                // For regular forms, use JSON
                const jsonData = Object.fromEntries(formData);
                data = await fetchAPI(url, {
                    method: method,
                    body: JSON.stringify(jsonData)
                });
            }
            
            hideSpinner();
            
            if (data.success) {
                showToast(data.message, 'success');
                
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else if (callback) {
                    callback(data);
                }
            } else {
                if (data.errors) {
                    displayFormErrors(formId, data.errors);
                } else {
                    showToast(data.message || 'Operation failed', 'error');
                }
            }
        } catch (error) {
            hideSpinner();
            showToast(error.message || 'An error occurred', 'error');
            console.error('Form submit error:', error);
        }
    });
}

// Display Form Errors
function displayFormErrors(formId, errors) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    // Clear previous errors
    clearFormErrors(formId);
    
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

// Clear Form Errors
function clearFormErrors(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    form.querySelectorAll('.form-control').forEach(el => el.classList.remove('error'));
}

// Update Cart Badge
function updateCartBadge() {
    fetch('/cart/count')
        .then(res => res.json())
        .then(data => {
            const badge = document.querySelector('.cart-badge');
            if (badge) {
                badge.textContent = data.count || 0;
            }
        })
        .catch(err => console.error('Cart count error:', err));
}

// Confirm Delete
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

// Image Preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0] && preview) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Generic DELETE Function
async function deleteItem(url, message = 'Delete this item?') {
    if (!confirm(message)) return;
    
    try {
        showSpinner();
        
        const response = await fetchAPI(url, {
            method: 'DELETE'
        });
        
        hideSpinner();
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        hideSpinner();
        showToast(error.message || 'Failed to delete', 'error');
    }
}

// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // Update cart badge on page load
    if (document.querySelector('.cart-badge')) {
        updateCartBadge();
    }
});