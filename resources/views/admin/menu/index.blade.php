@extends('layouts.app')

@section('title', 'Menu Management - Admin')

@section('content')
<div class="admin-menu-page">
    <div class="container-fluid">
        <div class="page-header">
            <h1>Menu Management</h1>
            <button class="btn btn-primary" onclick="showAddItemModal()">
                + Add Menu Item
            </button>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h2>Menu Items</h2>
                <div class="header-actions">
                    <input type="text" id="searchItems" class="search-input" placeholder="Search items...">
                    <select id="filterCategory" class="filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="card-body">
                <div id="loadingItems" class="loading-spinner" style="display: none;">
                    <div class="spinner"></div>
                </div>
                
                <table class="data-table" id="menuItemsTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Restaurant</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="menuItemsBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Item Modal -->
<div id="itemFormModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeItemFormModal()"></div>
    <div class="modal-content">
        <button class="modal-close" onclick="closeItemFormModal()">&times;</button>
        <div class="modal-header">
            <h2 id="modalTitle">Add Menu Item</h2>
        </div>
        <form id="itemForm" enctype="multipart/form-data">
            <input type="hidden" id="itemId">
            
            <div class="form-group">
                <label for="restaurant_id">Restaurant *</label>
                <select id="restaurant_id" class="form-control" required>
                    <option value="">Select Restaurant</option>
                    @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="name">Item Name *</label>
                <input type="text" id="name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price (TZS) *</label>
                <input type="number" id="price" class="form-control" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="preparation_time">Preparation Time (minutes)</label>
                <input type="number" id="preparation_time" class="form-control" value="15" min="1">
            </div>
            
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" id="image" class="form-control" accept="image/*" onchange="previewItemImage(this)">
                <img id="imagePreview" style="max-width: 200px; margin-top: 10px; display: none;">
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="is_available" checked>
                    <span>Available</span>
                </label>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeItemFormModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Save Item</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let menuItems = [];
let editingItemId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadMenuItems();
    
    document.getElementById('searchItems').addEventListener('input', debounce(filterItems, 300));
    document.getElementById('filterCategory').addEventListener('change', filterItems);
    document.getElementById('itemForm').addEventListener('submit', handleItemSubmit);
});

async function loadMenuItems() {
    showLoading('loadingItems');
    
    try {
        const response = await fetch('/admin/api/menu/items');
        const data = await response.json();
        
        if (data.success) {
            menuItems = data.items;
            displayMenuItems(menuItems);
        }
    } catch (error) {
        console.error('Error loading items:', error);
    } finally {
        hideLoading('loadingItems');
    }
}

function displayMenuItems(items) {
    const tbody = document.getElementById('menuItemsBody');
    
    tbody.innerHTML = items.map(item => `
        <tr>
            <td>
                ${item.image 
                    ? `<img src="/storage/${item.image}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">`
                    : '<div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center;">🍽️</div>'
                }
            </td>
            <td>${item.name}</td>
            <td>${item.category.name}</td>
            <td>${item.restaurant.name}</td>
            <td>TZS ${formatPrice(item.price)}</td>
            <td>
                <span class="status-badge ${item.is_available ? 'status-completed' : 'status-cancelled'}">
                    ${item.is_available ? 'Available' : 'Unavailable'}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline" onclick="editItem(${item.id})">Edit</button>
                <button class="btn btn-sm btn-danger-outline" onclick="deleteItem(${item.id}, '${item.name}')">Delete</button>
            </td>
        </tr>
    `).join('');
}

function filterItems() {
    const searchTerm = document.getElementById('searchItems').value.toLowerCase();
    const categoryId = document.getElementById('filterCategory').value;
    
    const filtered = menuItems.filter(item => {
        const matchesSearch = !searchTerm || 
            item.name.toLowerCase().includes(searchTerm) || 
            (item.description && item.description.toLowerCase().includes(searchTerm));
        
        const matchesCategory = !categoryId || item.category_id == categoryId;
        
        return matchesSearch && matchesCategory;
    });
    
    displayMenuItems(filtered);
}

function showAddItemModal() {
    editingItemId = null;
    document.getElementById('modalTitle').textContent = 'Add Menu Item';
    document.getElementById('itemForm').reset();
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('itemFormModal').style.display = 'flex';
}

function editItem(itemId) {
    const item = menuItems.find(i => i.id === itemId);
    if (!item) return;
    
    editingItemId = itemId;
    document.getElementById('modalTitle').textContent = 'Edit Menu Item';
    document.getElementById('restaurant_id').value = item.restaurant_id;
    document.getElementById('category_id').value = item.category_id;
    document.getElementById('name').value = item.name;
    document.getElementById('description').value = item.description || '';
    document.getElementById('price').value = item.price;
    document.getElementById('preparation_time').value = item.preparation_time;
    document.getElementById('is_available').checked = item.is_available;
    
    if (item.image) {
        document.getElementById('imagePreview').src = `/storage/${item.image}`;
        document.getElementById('imagePreview').style.display = 'block';
    }
    
    document.getElementById('itemFormModal').style.display = 'flex';
}

async function handleItemSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('restaurant_id', document.getElementById('restaurant_id').value);
    formData.append('category_id', document.getElementById('category_id').value);
    formData.append('name', document.getElementById('name').value);
    formData.append('description', document.getElementById('description').value);
    formData.append('price', document.getElementById('price').value);
    formData.append('preparation_time', document.getElementById('preparation_time').value);
    formData.append('is_available', document.getElementById('is_available').checked ? 1 : 0);
    
    const imageFile = document.getElementById('image').files[0];
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';
    
    try {
        const url = editingItemId 
            ? `/admin/api/menu/items/${editingItemId}`
            : '/admin/api/menu/items';
        
        const method = editingItemId ? 'POST' : 'POST';
        
        if (editingItemId) {
            formData.append('_method', 'PUT');
        }
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            closeItemFormModal();
            loadMenuItems();
        } else {
            showNotification(data.message || 'Failed to save item', 'error');
        }
    } catch (error) {
        showNotification('An error occurred', 'error');
        console.error('Error:', error);
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Save Item';
    }
}

async function deleteItem(itemId, itemName) {
    if (!confirm(`Are you sure you want to delete "${itemName}"?`)) {
        return;
    }
    
    try {
        const response = await fetch(`/admin/api/menu/items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            loadMenuItems();
        } else {
            showNotification(data.message || 'Failed to delete item', 'error');
        }
    } catch (error) {
        showNotification('An error occurred', 'error');
        console.error('Error:', error);
    }
}

function closeItemFormModal() {
    document.getElementById('itemFormModal').style.display = 'none';
}

function previewItemImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function formatPrice(price) {
    return parseFloat(price).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function showLoading(id) {
    document.getElementById(id).style.display = 'flex';
}

function hideLoading(id) {
    document.getElementById(id).style.display = 'none';
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span class="notification-icon">${type === 'success' ? '✓' : '✕'}</span>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 10);
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
@endpush