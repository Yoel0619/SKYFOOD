// CSRF Token Setup
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Mobile Menu Toggle
const navToggle = document.getElementById('navToggle');
const navMenu = document.getElementById('navMenu');

if (navToggle) {
    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
}

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (navMenu && navMenu.classList.contains('active')) {
        if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
            navMenu.classList.remove('active');
        }
    }
});

// Toast Notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `toast ${type} show`;
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Loading Spinner
function showSpinner() {
    document.getElementById('spinnerOverlay').classList.add('active');
}

function hideSpinner() {
    document.getElementById('spinnerOverlay').classList.remove('active');
}

// AJAX Helper Function
async function fetchAPI(url, options = {}) {
    showSpinner();
    
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    };
    
    const mergedOptions = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...options.headers
        }
    };
    
    try {
        const response = await fetch(url, mergedOptions);
        const data = await response.json();
        
        hideSpinner();
        
        if (!response.ok) {
            throw data;
        }
        
        return data;
    } catch (error) {
        hideSpinner();
        throw error;
    }
}

// Form Submission Helper
function handleFormSubmit(formId, successCallback) {
    const form = document.getElementById(formId);
    
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Clear previous errors
        clearFormErrors(form);
        
        const formData = new FormData(form);
        const url = form.getAttribute('action');
        const method = form.getAttribute('method') || 'POST';
        
        try {
            const data = await fetchAPI(url, {
                method: method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            if (data.success) {
                showToast(data.message, 'success');
                
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                }
                
                if (successCallback) {
                    successCallback(data);
                }
            }
        } catch (error) {
            if (error.errors) {
                displayFormErrors(form, error.errors);
            } else {
                showToast(error.message || 'An error occurred', 'error');
            }
        }
    });
}

// Display form errors
function displayFormErrors(form, errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const input = form.querySelector(`[name="${field}"]`);
        
        if (input) {
            input.classList.add('error');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = messages[0];
            
            input.parentNode.appendChild(errorDiv);
        }
    }
}

// Clear form errors
function clearFormErrors(form) {
    const errorInputs = form.querySelectorAll('.error');
    errorInputs.forEach(input => input.classList.remove('error'));
    
    const errorMessages = form.querySelectorAll('.error-message');
    errorMessages.forEach(msg => msg.remove());
}

// Confirm Delete
function confirmDelete(message = 'Are you sure you want to delete this?') {
    return confirm(message);
}

// Update Cart Badge
async function updateCartBadge() {
    try {
        const response = await fetch('/cart/count');
        const data = await response.json();
        
        const badge = document.getElementById('cartBadge');
        if (badge) {
            badge.textContent = data.count;
        }
    } catch (error) {
        console.error('Failed to update cart badge:', error);
    }
}

// Initialize cart badge on page load
if (document.getElementById('cartBadge')) {
    updateCartBadge();
}

// Image Preview
function previewImage(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    
    if (file && preview) {
        const reader = new FileReader();
        
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(file);
    }
}