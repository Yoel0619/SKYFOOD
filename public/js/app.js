/**
 * ============================================
 * FOOD ORDERING SYSTEM - MAIN JAVASCRIPT
 * ============================================
 */

// Global Configuration
const CONFIG = {
    API_BASE_URL: window.location.origin,
    DELIVERY_FEE: 2000,
    CURRENCY: 'TZS',
    CART_STORAGE_KEY: 'foodhub_cart',
    USER_STORAGE_KEY: 'foodhub_user'
};

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Format price with currency
 */
function formatPrice(price) {
    return parseFloat(price).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

/**
 * Format date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

/**
 * Format date and time
 */
function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Show notification
 */
function showNotification(message, type = 'success', duration = 3000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span class="notification-icon">${type === 'success' ? '✓' : '✕'}</span>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Remove after duration
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, duration);
}

/**
 * Show loading state
 */
function showLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = 'flex';
    }
}

/**
 * Hide loading state
 */
function hideLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = 'none';
    }
}

/**
 * Get CSRF token
 */
function getCsrfToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.getAttribute('content') : '';
}

/**
 * Make API request
 */
async function apiRequest(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    };
    
    if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.message || 'Request failed');
        }
        
        return result;
    } catch (error) {
        console.error('API Request Error:', error);
        throw error;
    }
}

// ============================================
// CART MANAGEMENT
// ============================================

class CartManager {
    constructor() {
        this.cart = this.loadCart();
        this.updateCartCount();
    }
    
    /**
     * Load cart from localStorage
     */
    loadCart() {
        const cartData = localStorage.getItem(CONFIG.CART_STORAGE_KEY);
        return cartData ? JSON.parse(cartData) : [];
    }
    
    /**
     * Save cart to localStorage
     */
    saveCart() {
        localStorage.setItem(CONFIG.CART_STORAGE_KEY, JSON.stringify(this.cart));
        this.updateCartCount();
    }
    
    /**
     * Add item to cart
     */
    addItem(item, quantity = 1) {
        const existingItemIndex = this.cart.findIndex(i => i.id === item.id);
        
        if (existingItemIndex > -1) {
            this.cart[existingItemIndex].quantity += quantity;
        } else {
            this.cart.push({
                id: item.id,
                name: item.name,
                price: item.price,
                image: item.image,
                restaurant_id: item.restaurant_id,
                restaurant_name: item.restaurant ? item.restaurant.name : '',
                quantity: quantity
            });
        }
        
        this.saveCart();
        showNotification(`${item.name} added to cart!`, 'success');
    }
    
    /**
     * Update item quantity
     */
    updateQuantity(itemId, quantity) {
        quantity = parseInt(quantity);
        
        if (quantity < 1) {
            this.removeItem(itemId);
            return;
        }
        
        if (quantity > 50) {
            showNotification('Maximum quantity is 50', 'error');
            return;
        }
        
        const itemIndex = this.cart.findIndex(i => i.id === itemId);
        if (itemIndex > -1) {
            this.cart[itemIndex].quantity = quantity;
            this.saveCart();
        }
    }
    
    /**
     * Remove item from cart
     */
    removeItem(itemId) {
        this.cart = this.cart.filter(item => item.id !== itemId);
        this.saveCart();
        showNotification('Item removed from cart', 'success');
    }
    
    /**
     * Clear cart
     */
    clearCart() {
        this.cart = [];
        this.saveCart();
    }
    
    /**
     * Get cart items
     */
    getItems() {
        return this.cart;
    }
    
    /**
     * Get cart total
     */
    getTotal() {
        return this.cart.reduce((total, item) => {
            return total + (parseFloat(item.price) * parseInt(item.quantity));
        }, 0);
    }
    
    /**
     * Get cart count
     */
    getCount() {
        return this.cart.reduce((count, item) => count + parseInt(item.quantity), 0);
    }
    
    /**
     * Update cart count in navbar
     */
    updateCartCount() {
        const cartCountEl = document.getElementById('cartCount');
        if (cartCountEl) {
            cartCountEl.textContent = this.getCount();
        }
    }
    
    /**
     * Validate cart (all items from same restaurant)
     */
    validateCart() {
        if (this.cart.length === 0) {
            return { valid: false, message: 'Cart is empty' };
        }
        
        const restaurantIds = [...new Set(this.cart.map(item => item.restaurant_id))];
        if (restaurantIds.length > 1) {
            return { 
                valid: false, 
                message: 'All items must be from the same restaurant' 
            };
        }
        
        return { valid: true };
    }
}

// Initialize global cart manager
const cartManager = new CartManager();

// ============================================
// AUTHENTICATION
// ============================================

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    const user = localStorage.getItem(CONFIG.USER_STORAGE_KEY);
    return user !== null;
}

/**
 * Get current user
 */
function getCurrentUser() {
    const userData = localStorage.getItem(CONFIG.USER_STORAGE_KEY);
    return userData ? JSON.parse(userData) : null;
}

/**
 * Logout user
 */
async function logout() {
    try {
        await apiRequest('/logout', 'POST');
        localStorage.removeItem(CONFIG.USER_STORAGE_KEY);
        window.location.href = '/';
    } catch (error) {
        console.error('Logout error:', error);
        showNotification('Logout failed', 'error');
    }
}

// ============================================
// FORM VALIDATION
// ============================================

/**
 * Validate email
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate phone number (Tanzania format)
 */
function validatePhone(phone) {
    const re = /^(\+255|0)[6-7][0-9]{8}$/;
    return re.test(phone.replace(/\s/g, ''));
}

/**
 * Show field error
 */
function showFieldError(fieldId, message) {
    const errorEl = document.getElementById(`${fieldId}-error`);
    if (errorEl) {
        errorEl.textContent = message;
    }
    
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.add('error');
    }
}

/**
 * Clear field error
 */
function clearFieldError(fieldId) {
    const errorEl = document.getElementById(`${fieldId}-error`);
    if (errorEl) {
        errorEl.textContent = '';
    }
    
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.remove('error');
    }
}

/**
 * Clear all form errors
 */
function clearAllErrors() {
    document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
    document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
}

// ============================================
// IMAGE UPLOAD & PREVIEW
// ============================================

/**
 * Preview image before upload
 */
function previewImage(input, previewElementId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById(previewElementId);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Validate image file
 */
function validateImageFile(file) {
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    const maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!validTypes.includes(file.type)) {
        return { valid: false, message: 'Only JPG, PNG, and GIF files are allowed' };
    }
    
    if (file.size > maxSize) {
        return { valid: false, message: 'File size must be less than 2MB' };
    }
    
    return { valid: true };
}

// ============================================
// LOCAL STORAGE UTILITIES
// ============================================

/**
 * Save to localStorage with expiry
 */
function setWithExpiry(key, value, ttl) {
    const now = new Date();
    const item = {
        value: value,
        expiry: now.getTime() + ttl
    };
    localStorage.setItem(key, JSON.stringify(item));
}

/**
 * Get from localStorage with expiry check
 */
function getWithExpiry(key) {
    const itemStr = localStorage.getItem(key);
    
    if (!itemStr) {
        return null;
    }
    
    const item = JSON.parse(itemStr);
    const now = new Date();
    
    if (now.getTime() > item.expiry) {
        localStorage.removeItem(key);
        return null;
    }
    
    return item.value;
}

// ============================================
// SCROLL UTILITIES
// ============================================

/**
 * Smooth scroll to element
 */
function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Scroll to top
 */
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart count
    cartManager.updateCartCount();
    
    // Mobile menu toggle
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
            }
        });
    }
    
    // Close mobile menu when clicking on a link
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu) {
                navMenu.classList.remove('active');
            }
        });
    });
    
    // Add active class to current page link
    const currentPath = window.location.pathname;
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active-link');
        }
    });
    
    // Initialize tooltips (if any)
    initializeTooltips();
    
    // Initialize lazy loading for images
    initializeLazyLoading();
    
    // Add scroll-to-top button
    addScrollToTopButton();
});

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltipText = this.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = tooltipText;
            this.appendChild(tooltip);
        });
        
        element.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });
}

/**
 * Initialize lazy loading for images
 */
function initializeLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        const lazyImages = document.querySelectorAll('img.lazy');
        lazyImages.forEach(img => imageObserver.observe(img));
    }
}

/**
 * Add scroll to top button
 */
function addScrollToTopButton() {
    const button = document.createElement('button');
    button.innerHTML = '↑';
    button.className = 'scroll-to-top';
    button.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        display: none;
        z-index: 1000;
        box-shadow: var(--shadow-lg);
        transition: var(--transition);
    `;
    
    button.addEventListener('click', scrollToTop);
    document.body.appendChild(button);
    
    // Show/hide button on scroll
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            button.style.display = 'block';
        } else {
            button.style.display = 'none';
        }
    });
}

// ============================================
// EXPORT FOR USE IN OTHER FILES
// ============================================

window.FoodHub = {
    CONFIG,
    formatPrice,
    formatDate,
    formatDateTime,
    debounce,
    showNotification,
    showLoading,
    hideLoading,
    apiRequest,
    cartManager,
    isAuthenticated,
    getCurrentUser,
    logout,
    validateEmail,
    validatePhone,
    showFieldError,
    clearFieldError,
    clearAllErrors,
    previewImage,
    validateImageFile,
    smoothScrollTo,
    scrollToTop
};