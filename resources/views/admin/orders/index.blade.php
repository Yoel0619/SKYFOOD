@extends('layouts.app')

@section('title', 'Order Management - Admin')

@section('content')
<div class="admin-orders-page">
    <div class="container-fluid">
        <div class="page-header">
            <h1>Order Management</h1>
            <div class="header-stats">
                <div class="stat-badge">
                    <span class="stat-label">Total Orders:</span>
                    <span class="stat-value" id="totalOrdersCount">0</span>
                </div>
                <div class="stat-badge">
                    <span class="stat-label">Pending:</span>
                    <span class="stat-value" id="pendingOrdersCount">0</span>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="admin-card">
            <div class="card-header">
                <h2>Filters</h2>
            </div>
            <div class="card-body">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label>Search</label>
                        <input type="text" id="searchOrders" class="form-control" placeholder="Search by Order ID or Customer...">
                    </div>
                    
                    <div class="filter-group">
                        <label>Status</label>
                        <select id="filterStatus" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="delivering">Delivering</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Payment Status</label>
                        <select id="filterPayment" class="form-control">
                            <option value="">All Payment Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>From Date</label>
                        <input type="date" id="fromDate" class="form-control">
                    </div>
                    
                    <div class="filter-group">
                        <label>To Date</label>
                        <input type="date" id="toDate" class="form-control">
                    </div>
                    
                    <div class="filter-group" style="display: flex; align-items: flex-end;">
                        <button class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
                        <button class="btn btn-outline" onclick="clearFilters()" style="margin-left: 10px;">Clear</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="admin-card">
            <div class="card-header">
                <h2>Orders List</h2>
                <button class="btn btn-outline-sm" onclick="loadOrders()">
                    🔄 Refresh
                </button>
            </div>
            
            <div class="card-body">
                <div id="loadingOrders" class="loading-spinner" style="display: none;">
                    <div class="spinner"></div>
                    <p>Loading orders...</p>
                </div>
                
                <div id="ordersTableContainer">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Restaurant</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                        </tbody>
                    </table>
                </div>
                
                <div id="noOrders" class="empty-state" style="display: none;">
                    <div class="empty-icon">📦</div>
                    <h3>No orders found</h3>
                    <p>No orders match your filters</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderDetailsModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeOrderDetailsModal()"></div>
    <div class="modal-content modal-lg">
        <button class="modal-close" onclick="closeOrderDetailsModal()">&times;</button>
        <div id="orderDetailsContent">
            <!-- Order details will be loaded here -->
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateStatusModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeUpdateStatusModal()"></div>
    <div class="modal-content">
        <button class="modal-close" onclick="closeUpdateStatusModal()">&times;</button>
        <div class="modal-header">
            <h2>Update Order Status</h2>
        </div>
        <form id="updateStatusForm">
            <input type="hidden" id="updateOrderId">
            
            <div class="form-group">
                <label>Current Status: <strong id="currentStatus"></strong></label>
            </div>
            
            <div class="form-group">
                <label for="newStatus">New Status *</label>
                <select id="newStatus" class="form-control" required>
                    <option value="">Select Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="preparing">Preparing</option>
                    <option value="ready">Ready for Pickup</option>
                    <option value="delivering">Out for Delivery</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="newPaymentStatus">Payment Status *</label>
                <select id="newPaymentStatus" class="form-control" required>
                    <option value="">Select Payment Status</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeUpdateStatusModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="updateStatusBtn">Update Status</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allOrders = [];
let currentFilters = {
    search: '',
    status: '',
    payment_status: '',
    from_date: '',
    to_date: ''
};

document.addEventListener('DOMContentLoaded', function() {
    loadOrders();
    
    document.getElementById('updateStatusForm').addEventListener('submit', handleStatusUpdate);
});

// ============================================
// LOAD ORDERS (READ)
// ============================================
async function loadOrders() {
    const loadingEl = document.getElementById('loadingOrders');
    const tableContainer = document.getElementById('ordersTableContainer');
    const noOrdersEl = document.getElementById('noOrders');
    
    loadingEl.style.display = 'flex';
    tableContainer.style.display = 'none';
    noOrdersEl.style.display = 'none';
    
    try {
        // Build query params
        const params = new URLSearchParams();
        if (currentFilters.search) params.append('search', currentFilters.search);
        if (currentFilters.status) params.append('status', currentFilters.status);
        if (currentFilters.payment_status) params.append('payment_status', currentFilters.payment_status);
        if (currentFilters.from_date) params.append('from_date', currentFilters.from_date);
        if (currentFilters.to_date) params.append('to_date', currentFilters.to_date);
        
        const response = await fetch(`/admin/api/orders?${params.toString()}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            allOrders = data.orders;
            displayOrders(allOrders);
            updateStats(allOrders);
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        showNotification('Failed to load orders', 'error');
    } finally {
        loadingEl.style.display = 'none';
    }
}

function displayOrders(orders) {
    const tbody = document.getElementById('ordersTableBody');
    const tableContainer = document.getElementById('ordersTableContainer');
    const noOrdersEl = document.getElementById('noOrders');
    
    if (orders.length === 0) {
        tableContainer.style.display = 'none';
        noOrdersEl.style.display = 'block';
        return;
    }
    
    tableContainer.style.display = 'block';
    noOrdersEl.style.display = 'none';
    
    tbody.innerHTML = orders.map(order => `
        <tr>
            <td><strong>#${order.id}</strong></td>
            <td>
                ${order.user.name}<br>
                <small style="color: #6b7280;">${order.user.email}</small>
            </td>
            <td>${order.restaurant.name}</td>
            <td>${order.items.length} item(s)</td>
            <td><strong>TZS ${formatPrice(order.total_amount)}</strong></td>
            <td>${formatPaymentMethod(order.payment_method)}</td>
            <td>
                <span class="status-badge status-${order.status}">
                    ${formatStatus(order.status)}
                </span>
            </td>
            <td>
                <span class="payment-badge payment-${order.payment_status}">
                    ${formatPaymentStatus(order.payment_status)}
                </span>
            </td>
            <td>${formatDateTime(order.created_at)}</td>
            <td>
                <div style="display: flex; gap: 5px;">
                    <button class="btn btn-sm btn-outline" onclick="viewOrderDetails(${order.id})" title="View Details">
                        👁️
                    </button>
                    <button class="btn btn-sm btn-primary" onclick="showUpdateStatusModal(${order.id})" title="Update Status">
                        ✏️
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function updateStats(orders) {
    document.getElementById('totalOrdersCount').textContent = orders.length;
    document.getElementById('pendingOrdersCount').textContent = 
        orders.filter(o => o.status === 'pending').length;
}

// ============================================
// VIEW ORDER DETAILS (READ)
// ============================================
async function viewOrderDetails(orderId) {
    const modal = document.getElementById('orderDetailsModal');
    const content = document.getElementById('orderDetailsContent');
    
    modal.style.display = 'flex';
    content.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';
    
    try {
        const order = allOrders.find(o => o.id === orderId);
        
        if (!order) {
            throw new Error('Order not found');
        }
        
        content.innerHTML = `
            <div class="order-details-admin">
                <div class="order-details-header">
                    <div>
                        <h2>Order #${order.id}</h2>
                        <p class="order-date">${formatDateTime(order.created_at)}</p>
                    </div>
                    <div>
                        <span class="status-badge status-${order.status}">${formatStatus(order.status)}</span>
                        <span class="payment-badge payment-${order.payment_status}" style="margin-left: 10px;">
                            ${formatPaymentStatus(order.payment_status)}
                        </span>
                    </div>
                </div>
                
                <!-- Customer Information -->
                <div class="info-section">
                    <h3>Customer Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Name:</strong> ${order.user.name}
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong> ${order.user.email}
                        </div>
                        <div class="info-item">
                            <strong>Phone:</strong> ${order.phone}
                        </div>
                        <div class="info-item">
                            <strong>Delivery Address:</strong><br>
                            ${order.delivery_address}
                        </div>
                    </div>
                </div>
                
                <!-- Restaurant Information -->
                <div class="info-section">
                    <h3>Restaurant Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Name:</strong> ${order.restaurant.name}
                        </div>
                        <div class="info-item">
                            <strong>Phone:</strong> ${order.restaurant.phone || 'N/A'}
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="info-section">
                    <h3>Order Items</h3>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${order.items.map(item => `
                                <tr>
                                    <td>
                                        <strong>${item.menu_item.name}</strong>
                                        ${item.special_instructions ? `<br><small style="color: #6b7280; font-style: italic;">Note: ${item.special_instructions}</small>` : ''}
                                    </td>
                                    <td>${item.quantity}</td>
                                    <td>TZS ${formatPrice(item.unit_price)}</td>
                                    <td><strong>TZS ${formatPrice(item.subtotal)}</strong></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                
                <!-- Payment Summary -->
                <div class="info-section">
                    <h3>Payment Summary</h3>
                    <div class="payment-summary-admin">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>TZS ${formatPrice(order.items.reduce((sum, item) => sum + parseFloat(item.subtotal), 0))}</span>
                        </div>
                        <div class="summary-row">
                            <span>Delivery Fee:</span>
                            <span>TZS 2,000</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span>TZS ${formatPrice(order.total_amount)}</span>
                        </div>
                        <div class="payment-method">
                            <strong>Payment Method:</strong> ${formatPaymentMethod(order.payment_method)}
                        </div>
                        ${order.notes ? `
                            <div class="order-notes">
                                <strong>Customer Notes:</strong><br>
                                ${order.notes}
                            </div>
                        ` : ''}
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="order-actions">
                    <button class="btn btn-primary" onclick="closeOrderDetailsModal(); showUpdateStatusModal(${order.id});">
                        Update Status
                    </button>
                    <button class="btn btn-outline" onclick="printOrder(${order.id})">
                        🖨️ Print Order
                    </button>
                </div>
            </div>
        `;
    } catch (error) {
        content.innerHTML = '<p class="error-message">Failed to load order details.</p>';
        console.error('Error:', error);
    }
}

function closeOrderDetailsModal() {
    document.getElementById('orderDetailsModal').style.display = 'none';
}

// ============================================
// UPDATE ORDER STATUS (UPDATE)
// ============================================
function showUpdateStatusModal(orderId) {
    const order = allOrders.find(o => o.id === orderId);
    
    if (!order) {
        showNotification('Order not found', 'error');
        return;
    }
    
    document.getElementById('updateOrderId').value = orderId;
    document.getElementById('currentStatus').textContent = formatStatus(order.status);
    document.getElementById('newStatus').value = order.status;
    document.getElementById('newPaymentStatus').value = order.payment_status;
    
    document.getElementById('updateStatusModal').style.display = 'flex';
}

function closeUpdateStatusModal() {
    document.getElementById('updateStatusModal').style.display = 'none';
}

async function handleStatusUpdate(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('updateOrderId').value;
    const newStatus = document.getElementById('newStatus').value;
    const newPaymentStatus = document.getElementById('newPaymentStatus').value;
    const submitBtn = document.getElementById('updateStatusBtn');
    
    if (!newStatus || !newPaymentStatus) {
        showNotification('Please select both status and payment status', 'error');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    try {
        // Update order status
        const statusResponse = await fetch(`/admin/api/orders/${orderId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: newStatus })
        });
        
        const statusData = await statusResponse.json();
        
        if (!statusData.success) {
            throw new Error(statusData.message || 'Failed to update order status');
        }
        
        // Update payment status (if route exists)
        // You can add this route if needed
        
        showNotification('Order status updated successfully', 'success');
        closeUpdateStatusModal();
        loadOrders(); // Reload orders
        
    } catch (error) {
        showNotification(error.message || 'Failed to update order status', 'error');
        console.error('Error:', error);
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Update Status';
    }
}

// ============================================
// FILTERS
// ============================================
function applyFilters() {
    currentFilters = {
        search: document.getElementById('searchOrders').value,
        status: document.getElementById('filterStatus').value,
        payment_status: document.getElementById('filterPayment').value,
        from_date: document.getElementById('fromDate').value,
        to_date: document.getElementById('toDate').value
    };
    
    loadOrders();
}

function clearFilters() {
    document.getElementById('searchOrders').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterPayment').value = '';
    document.getElementById('fromDate').value = '';
    document.getElementById('toDate').value = '';
    
    currentFilters = {
        search: '',
        status: '',
        payment_status: '',
        from_date: '',
        to_date: ''
    };
    
    loadOrders();
}

// ============================================
// PRINT ORDER
// ============================================
function printOrder(orderId) {
    const order = allOrders.find(o => o.id === orderId);
    
    if (!order) {
        showNotification('Order not found', 'error');
        return;
    }
    
    const printWindow = window.open('', '_blank');
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Order #${order.id} - FoodHub</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h1 { border-bottom: 2px solid #333; padding-bottom: 10px; }
                .info-section { margin: 20px 0; }
                .info-section h3 { background: #f0f0f0; padding: 8px; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background: #f0f0f0; }
                .total { font-size: 1.2em; font-weight: bold; text-align: right; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <h1>Order #${order.id}</h1>
            <p><strong>Date:</strong> ${formatDateTime(order.created_at)}</p>
            <p><strong>Status:</strong> ${formatStatus(order.status)}</p>
            
            <div class="info-section">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> ${order.user.name}</p>
                <p><strong>Phone:</strong> ${order.phone}</p>
                <p><strong>Delivery Address:</strong> ${order.delivery_address}</p>
            </div>
            
            <div class="info-section">
                <h3>Restaurant</h3>
                <p>${order.restaurant.name}</p>
            </div>
            
            <div class="info-section">
                <h3>Order Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${order.items.map(item => `
                            <tr>
                                <td>${item.menu_item.name}</td>
                                <td>${item.quantity}</td>
                                <td>TZS ${formatPrice(item.unit_price)}</td>
                                <td>TZS ${formatPrice(item.subtotal)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            
            <div class="total">
                Total: TZS ${formatPrice(order.total_amount)}
            </div>
            
            <div class="info-section">
                <p><strong>Payment Method:</strong> ${formatPaymentMethod(order.payment_method)}</p>
                <p><strong>Payment Status:</strong> ${formatPaymentStatus(order.payment_status)}</p>
            </div>
            
            <div class="no-print" style="margin-top: 30px;">
                <button onclick="window.print()">Print</button>
                <button onclick="window.close()">Close</button>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
}

// ============================================
// UTILITY FUNCTIONS
// ============================================
function formatStatus(status) {
    const statusMap = {
        'pending': 'Pending',
        'confirmed': 'Confirmed',
        'preparing': 'Preparing',
        'ready': 'Ready',
        'delivering': 'Delivering',
        'completed': 'Completed',
        'cancelled': 'Cancelled'
    };
    return statusMap[status] || status;
}

function formatPaymentStatus(status) {
    const statusMap = {
        'pending': 'Pending',
        'paid': 'Paid',
        'failed': 'Failed'
    };
    return statusMap[status] || status;
}

function formatPaymentMethod(method) {
    const methodMap = {
        'cash': 'Cash on Delivery',
        'mobile_money': 'Mobile Money',
        'card': 'Credit/Debit Card'
    };
    return methodMap[method] || method;
}

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

function formatPrice(price) {
    return parseFloat(price).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
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

@push('styles')
<style>
.admin-orders-page {
    padding: 2rem 0;
}

.header-stats {
    display: flex;
    gap: 1rem;
}

.stat-badge {
    background: white;
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
}

.stat-label {
    color: var(--gray-600);
    font-size: 0.9rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary);
    margin-left: 0.5rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.admin-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
}

.card-header {
    padding: 1.5rem;
    border-bottom: 2px solid var(--gray-100);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-body {
    padding: 1.5rem;
}

.order-details-admin {
    padding: 2rem;
}

.order-details-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--gray-200);
}

.info-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--gray-50);
    border-radius: var(--radius-md);
}

.info-section h3 {
    margin-bottom: 1rem;
    color: var(--gray-800);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    font-size: 0.95rem;
}

.info-item strong {
    color: var(--gray-700);
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.items-table th,
.items-table td {
    padding: 0.75rem;
    border: 1px solid var(--gray-200);
    text-align: left;
}

.items-table th {
    background: var(--gray-100);
    font-weight: 600;
}

.payment-summary-admin {
    background: white;
    padding: 1rem;
    border-radius: var(--radius-md);
}

.payment-summary-admin .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    font-size: 1.1rem;
}

.payment-summary-admin .total {
    border-top: 2px solid var(--gray-300);
    margin-top: 0.5rem;
    padding-top: 1rem;
    font-size: 1.3rem;
    font-weight: bold;
    color: var(--primary);
}

.payment-method,
.order-notes {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.order-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid var(--gray-200);
}
</style>
@endpush