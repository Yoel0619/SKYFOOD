@extends('layouts.app')

@section('title', 'Menu - FoodHub')

@section('content')
<div class="menu-page">
    <!-- Hero Section -->
    <section class="menu-hero">
        <div class="container">
            <h1>Our Menu</h1>
            <p>Discover delicious meals from the best restaurants</p>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-wrapper">
                <!-- Search -->
                <div class="filter-group">
                    <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search menu items...">
                </div>

                <!-- Category Filter -->
                <div class="filter-group">
                    <select id="categoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Restaurant Filter -->
                <div class="filter-group">
                    <select id="restaurantFilter" class="filter-select">
                        <option value="">All Restaurants</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Price Filter -->
                <div class="filter-group">
                    <select id="priceFilter" class="filter-select">
                        <option value="">All Prices</option>
                        <option value="5000">Under TZS 5,000</option>
                        <option value="10000">Under TZS 10,000</option>
                        <option value="20000">Under TZS 20,000</option>
                        <option value="50000">Under TZS 50,000</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <button id="clearFilters" class="btn btn-outline-sm">Clear Filters</button>
            </div>
        </div>
    </section>

    <!-- Menu Items Grid -->
    <section class="menu-items-section">
        <div class="container">
            <div id="loadingSpinner" class="loading-spinner" style="display: none;">
                <div class="spinner"></div>
                <p>Loading menu items...</p>
            </div>

            <div id="menuGrid" class="menu-grid">
                <!-- Menu items will be loaded here via JavaScript -->
            </div>

            <div id="noResults" class="no-results" style="display: none;">
                <div class="no-results-icon">😕</div>
                <h3>No items found</h3>
                <p>Try adjusting your filters or search terms</p>
            </div>
        </div>
    </section>
</div>

<!-- Item Details Modal -->
<div id="itemModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeItemModal()"></div>
    <div class="modal-content">
        <button class="modal-close" onclick="closeItemModal()">&times;</button>
        <div id="itemModalContent">
            <!-- Item details will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Menu Items Management
let menuItems = [];
let filteredItems = [];

// Load menu items on page load
document.addEventListener('DOMContentLoaded', function() {
    loadMenuItems();
    
    // Setup event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(filterItems, 300));
    document.getElementById('categoryFilter').addEventListener('change', filterItems);
    document.getElementById('restaurantFilter').addEventListener('change', filterItems);
    document.getElementById('priceFilter').addEventListener('change', filterItems);
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
});

// Load menu items from API
async function loadMenuItems() {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const menuGrid = document.getElementById('menuGrid');
    
    loadingSpinner.style.display = 'block';
    menuGrid.innerHTML = '';
    
    try {
        const response = await fetch('/api/menu/items');
        const data = await response.json();
        
        if (data.success) {
            menuItems = data.items;
            filteredItems = menuItems;
            displayMenuItems(filteredItems);
        }
    } catch (error) {
        console.error('Error loading menu items:', error);
        menuGrid.innerHTML = '<p class="error-message">Failed to load menu items. Please refresh the page.</p>';
    } finally {
        loadingSpinner.style.display = 'none';
    }
}

// Display menu items
function displayMenuItems(items) {
    const menuGrid = document.getElementById('menuGrid');
    const noResults = document.getElementById('noResults');
    
    if (items.length === 0) {
        menuGrid.innerHTML = '';
        noResults.style.display = 'block';
        return;
    }
    
    noResults.style.display = 'none';
    
    menuGrid.innerHTML = items.map(item => `
        <div class="menu-card" data-item-id="${item.id}">
            <div class="menu-card-image">
                ${item.image 
                    ? `<img src="/storage/${item.image}" alt="${item.name}">`
                    : `<div class="menu-card-placeholder">🍽️</div>`
                }
                ${!item.is_available ? '<div class="unavailable-badge">Unavailable</div>' : ''}
            </div>
            <div class="menu-card-content">
                <div class="menu-card-header">
                    <h3 class="menu-card-title">${item.name}</h3>
                    <span class="menu-card-category">${item.category.name}</span>
                </div>
                <p class="menu-card-description">${item.description || 'Delicious and freshly prepared'}</p>
                <div class="menu-card-meta">
                    <span class="restaurant-name">📍 ${item.restaurant.name}</span>
                    <span class="prep-time">⏱️ ${item.preparation_time} min</span>
                </div>
                <div class="menu-card-footer">
                    <div class="price">TZS ${formatPrice(item.price)}</div>
                    ${item.is_available 
                        ? `<button class="btn btn-primary btn-sm" onclick="addToCart(${item.id})">
                            Add to Cart
                           </button>`
                        : `<button class="btn btn-disabled btn-sm" disabled>Unavailable</button>`
                    }
                </div>
            </div>
            <button class="quick-view-btn" onclick="viewItemDetails(${item.id})">
                Quick View
            </button>
        </div>
    `).join('');
}

// Filter items
function filterItems() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryId = document.getElementById('categoryFilter').value;
    const restaurantId = document.getElementById('restaurantFilter').value;
    const maxPrice = document.getElementById('priceFilter').value;
    
    filteredItems = menuItems.filter(item => {
        const matchesSearch = !searchTerm || 
            item.name.toLowerCase().includes(searchTerm) || 
            (item.description && item.description.toLowerCase().includes(searchTerm));
        
        const matchesCategory = !categoryId || item.category_id == categoryId;
        const matchesRestaurant = !restaurantId || item.restaurant_id == restaurantId;
        const matchesPrice = !maxPrice || parseFloat(item.price) <= parseFloat(maxPrice);
        
        return matchesSearch && matchesCategory && matchesRestaurant && matchesPrice;
    });
    
    displayMenuItems(filteredItems);
}

// Clear all filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('restaurantFilter').value = '';
    document.getElementById('priceFilter').value = '';
    
    filteredItems = menuItems;
    displayMenuItems(filteredItems);
}

// View item details
async function viewItemDetails(itemId) {
    const modal = document.getElementById('itemModal');
    const modalContent = document.getElementById('itemModalContent');
    
    modal.style.display = 'flex';
    modalContent.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';
    
    try {
        const response = await fetch(`/api/menu/items/${itemId}`);
        const data = await response.json();
        
        if (data.success) {
            const item = data.item;
            modalContent.innerHTML = `
                <div class="item-details">
                    <div class="item-details-image">
                        ${item.image 
                            ? `<img src="/storage/${item.image}" alt="${item.name}">`
                            : `<div class="item-details-placeholder">🍽️</div>`
                        }
                    </div>
                    <div class="item-details-info">
                        <h2>${item.name}</h2>
                        <span class="category-badge">${item.category.name}</span>
                        
                        <p class="item-description">${item.description || 'Delicious and freshly prepared'}</p>
                        
                        <div class="item-meta">
                            <div class="meta-item">
                                <strong>Restaurant:</strong> ${item.restaurant.name}
                            </div>
                            <div class="meta-item">
                                <strong>Preparation Time:</strong> ${item.preparation_time} minutes
                            </div>
                            <div class="meta-item">
                                <strong>Availability:</strong> 
                                <span class="${item.is_available ? 'text-success' : 'text-danger'}">
                                    ${item.is_available ? 'Available' : 'Unavailable'}
                                </span>
                            </div>
                        </div>
                        
                        <div class="item-price-section">
                            <div class="item-price">TZS ${formatPrice(item.price)}</div>
                            ${item.is_available 
                                ? `<div class="quantity-selector">
                                    <button onclick="decrementQuantity()" class="qty-btn">-</button>
                                    <input type="number" id="itemQuantity" value="1" min="1" max="50" class="qty-input">
                                    <button onclick="incrementQuantity()" class="qty-btn">+</button>
                                   </div>
                                   <button class="btn btn-primary btn-lg" onclick="addToCartFromModal(${item.id})">
                                       Add to Cart
                                   </button>`
                                : `<button class="btn btn-disabled btn-lg" disabled>Currently Unavailable</button>`
                            }
                        </div>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        modalContent.innerHTML = '<p class="error-message">Failed to load item details.</p>';
        console.error('Error:', error);
    }
}

// Close modal
function closeItemModal() {
    document.getElementById('itemModal').style.display = 'none';
}

// Quantity controls
function incrementQuantity() {
    const input = document.getElementById('itemQuantity');
    input.value = Math.min(parseInt(input.value) + 1, 50);
}

function decrementQuantity() {
    const input = document.getElementById('itemQuantity');
    input.value = Math.max(parseInt(input.value) - 1, 1);
}

// Add to cart from modal
function addToCartFromModal(itemId) {
    const quantity = parseInt(document.getElementById('itemQuantity').value);
    addToCart(itemId, quantity);
    closeItemModal();
}

// Add to cart function
function addToCart(itemId, quantity = 1) {
    const item = menuItems.find(i => i.id === itemId);
    
    if (!item) {
        showNotification('Item not found', 'error');
        return;
    }
    
    // Get existing cart or create new one
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check if item already in cart
    const existingItemIndex = cart.findIndex(i => i.id === itemId);
    
    if (existingItemIndex > -1) {
        cart[existingItemIndex].quantity += quantity;
    } else {
        cart.push({
            id: item.id,
            name: item.name,
            price: item.price,
            image: item.image,
            restaurant_id: item.restaurant_id,
            restaurant_name: item.restaurant.name,
            quantity: quantity
        });
    }
    
    // Save cart
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update cart count
    updateCartCount();
    
    // Show notification
    showNotification(`${item.name} added to cart!`, 'success');
}

// Update cart count in navbar
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    const cartCountEl = document.getElementById('cartCount');
    if (cartCountEl) {
        cartCountEl.textContent = totalItems;
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span class="notification-icon">${type === 'success' ? '✓' : '✕'}</span>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Format price
function formatPrice(price) {
    return parseFloat(price).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

// Debounce function for search
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

// Update cart count on page load
updateCartCount();
</script>
@endpush