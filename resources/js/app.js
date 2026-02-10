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

    try {
        const response = await fetch(url, mergedOptions);
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Request failed');
        }
        
        return data;
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
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
        const hasFiles = Array.from(formData.entries()).some(([key, value]) => value instanceof File);
        
        try {
            showSpinner();
            
            let response;
            
            if (hasFiles) {
                // For file uploads, use FormData
                response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
            } else {
                // For regular forms, use JSON
                const data = Object.fromEntries(formData);
                response = await fetchAPI(url, {
                    method: method,
                    body: JSON.stringify(data)
                });
            }
            
            // Handle file upload response
            if (hasFiles) {
                response = await response.json();
            }
            
            hideSpinner();
            
            if (response.success) {
                showToast(response.message, 'success');
                
                if (response.redirect) {
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1000);
                } else if (callback) {
                    callback(response);
                }
            } else {
                if (response.errors) {
                    displayFormErrors(formId, response.errors);
                } else {
                    showToast(response.message || 'Operation failed', 'error');
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
        };
        
        reader.readAsDataURL(input.files[0]);
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
    updateCartBadge();
});    
// DELETE FUNCTIONS FOR ALL RESOURCES

// Delete Product
async function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product?')) return;
    
    try {
        showSpinner();
        
        const response = await fetch(`/products/${productId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        
        const result = await response.json();
        
        hideSpinner();
        
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message || 'Failed to delete product', 'error');
        }
    } catch (error) {
        hideSpinner();
        showToast('Failed to delete product', 'error');
        console.error('Delete error:', error);
    }
}

// Delete Category
async function deleteCategory(categoryId) {
    if (!confirm('Are you sure you want to delete this category?')) return;
    
    try {
        showSpinner();
        
        const response = await fetch(`/categories/${categoryId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        
        const result = await response.json();
        
        hideSpinner();
        
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message || 'Failed to delete category', 'error');
        }
    } catch (error) {
        hideSpinner();
        showToast('Failed to delete category', 'error');
        console.error('Delete error:', error);
    }
}

// Delete User
async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user?')) return;
    
    try {
        showSpinner();
        
        const response = await fetch(`/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        
        const result = await response.json();
        
        hideSpinner();
        
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message || 'Failed to delete user', 'error');
        }
    } catch (error) {
        hideSpinner();
        showToast('Failed to delete user', 'error');
        console.error('Delete error:', error);
    }
}

// Delete Role
async function deleteRole(roleId) {
    if (!confirm('Are you sure you want to delete this role?')) return;
    
    try {
        showSpinner();
        
        const response = await fetch(`/roles/${roleId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        
        const result = await response.json();
        
        hideSpinner();
        
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message || 'Failed to delete role', 'error');
        }
    } catch (error) {
        hideSpinner();
        showToast('Failed to delete role', 'error');
        console.error('Delete error:', error);
    }
}

// Delete Order
async function deleteOrder(orderId) {
    if (!confirm('Are you sure you want to delete this order? This cannot be undone!')) return;
    
    try {
        showSpinner();
        
        const response = await fetch(`/orders/${orderId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        
        const result = await response.json();
        
        hideSpinner();
        
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message || 'Failed to delete order', 'error');
        }
    } catch (error) {
        hideSpinner();
        showToast('Failed to delete order', 'error');
        console.error('Delete error:', error);
    }
}

// Delete Payment
async function deletePayment(paymentId) {
    if (!confirm('Are you sure you want to delete this payment record?')) return;
    
    try {
        showSpinner();
        
        const response = await fetch(`/payments/${paymentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        
        const result = await response.json();
        
        hideSpinner();
        
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message || 'Failed to delete payment', 'error');
        }
    } catch (error) {
        hideSpinner();
        showToast('Failed to delete payment', 'error');
        console.error('Delete error:', error);
    }
}

// Delete Delivery
async function deleteDelivery(deliveryId) {
    if (!confirm('Are you sure you want to delete this delivery?')) return;
    
    try {
        showSpinner();
        
        const response = await fetch(`/deliveries/${deliveryId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        
        const result = await response.json();
        
        hideSpinner();
        
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message || 'Failed to delete delivery', 'error');
        }
    } catch (error) {
        hideSpinner();
        showToast('Failed to delete delivery', 'error');
        console.error('Delete error:', error);
    }
}

// Cancel Order (Customer)
async function cancelOrder(orderId) {
    if (!confirm('Are you sure you want to cancel this order?')) return;
    
    try {
        showSpinner();
        
        const response = await fetch(`/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });
        
        const result = await response.json();
        
        hideSpinner();
        
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.message || 'Failed to cancel order', 'error');
        }
    } catch (error) {
        hideSpinner();
        showToast('Failed to cancel order', 'error');
        console.error('Cancel error:', error);
    }
}

// Add to Cart
async function addToCart(productId, quantity = 1) {
    try {
        showSpinner();
        
        const response = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });
        
        const result = await response.json();
        
        hideSpinner();
        
        if (result.success) {
            showToast(result.message, 'success');
            updateCartBadge();
        } else {
            showToast(result.message || 'Failed to add to cart', 'error');
        }
    } catch (error) {
        hideSpinner();
        showToast('Failed to add to cart', 'error');
        console.error('Add to cart error:', error);
    }
}