@extends('layouts.app')

@section('title', 'My Orders - FoodHub')

@section('content')
<div class="orders-page">
    <div class="container">
        <div class="page-header">
            <h1>My Orders</h1>
            <p>Track and view your order history</p>
        </div>

        <!-- Order Status Filter -->
        <div class="orders-filter">
            <button class="filter-btn active" data-status="all" onclick="filterOrders('all')">
                All Orders
            </button>
            <button class="filter-btn" data-status="pending" onclick="filterOrders('pending')">
                Pending
            </button>
            <button class="filter-btn" data-status="confirmed" onclick="filterOrders('confirmed')">
                Confirmed
            </button>
            <button class="filter-btn" data-status="preparing" onclick="filterOrders('preparing')">
                Preparing
            </button>
            <button class="filter-btn" data-status="delivering" onclick="filterOrders('delivering')">
                Delivering
            </button>
            <button class="filter-btn" data-status="completed" onclick="filterOrders('completed')">
                Completed
            </button>
            <button class="filter-btn" data-status="cancelled" onclick="filterOrders('cancelled')">
                Cancelled
            </button>
        </div>

        <!-- Loading State -->
        <div id="loadingOrders" class="loading-spinner" style="display: none;">
            <div class="spinner"></div>
            <p>Loading your orders...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyOrders" class="empty-state" style="display: none;">
            <div class="empty-icon">📦</div>
            <h2>No orders found</h2>
            <p>You haven't placed any orders yet</p>
            <a href="{{ route('menu.index') }}" class="btn btn-primary">Start Ordering</a>
        </div>

        <!-- Orders List -->
        <div id="ordersList" class="orders-list">
            <!-- Orders will be loaded here -->
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="orderModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeOrderModal()"></div>
    <div class="modal-content modal-lg">
        <button class="modal-close" onclick="closeOrderModal()">&times;</button>
        <div id="orderModalContent">
            <!-- Order details will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allOrders = [];
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', function() {
    loadOrders();
});

// Load orders from API
async function loadOrders() {
    const loadingEl = document.getElementById('loadingOrders');
    const emptyEl = document.getElementById('emptyOrders');
    const ordersListEl = document.getElementById('ordersList');
    
    loadingEl.style.display = 'block';
    emptyEl.style.display = 'none';
    ordersListEl.innerHTML = '';
    
    try {
        const response = await fetch('/api/orders', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            allOrders = data.orders;
            displayOrders(allOrders);
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        ordersListEl.innerHTML = '<p class="error-message">Failed to load orders. Please refresh the page.</p>';
    } finally {
        loadingEl.style.display = 'none';
    }
}

// Display orders
function displayOrders(orders) {
    const ordersListEl = document.getElementById('ordersList');
    const emptyEl = document.getElementById('emptyOrders');
    
    if (orders.length === 0) {
        ordersListEl.innerHTML = '';
        emptyEl.style.display = 'block';
        return;
    }
    
    emptyEl.style.display = 'none';
    
    ordersListEl.innerHTML = orders.map(order => `
        <div class="order-card" data-order-id="${order.id}">
            <div class="order-header">
                <div class="order-id-section">
                    <h3>Order #${order.id}</h3>
                    <span class="order-date">${formatDate(order.created_at)}</span>
                </div>
                <div class="order-status-section">
                    <span class="status-badge status-${order.status}">${formatStatus(order.status)}</span>
                    <span class="payment-badge payment-${order.payment_status}">${formatPaymentStatus(order.payment_status)}</span>
                </div>
            </div>
            
            <div class="order-body">
                <div class="order-info">
                    <div class="info-item">
                        <strong>Restaurant:</strong> ${order.restaurant.name}
                    </div>
                    <div class="info-item">
                        <strong>Items:</strong> ${order.items.length} item(s)
                    </div>
                    <div class="info-item">
                        <strong>Total:</strong> TZS ${formatPrice(order.total_amount)}
                    </div>
                    <div class="info-item">
                        <strong>Payment:</strong> ${formatPaymentMethod(order.payment_method)}
                    </div>
                </div>
                
                <div class="order-items-preview">
                    ${order.items.slice(0, 3).map(item => `
                        <div class="order-item-preview">
                            <span>${item.menu_item.name}</span>
                            <span class="item-qty">x${item.quantity}</span>
                        </div>
                    `).join('')}
                    ${order.items.length > 3 ? `<p class="more-items">+${order.items.length - 3} more item(s)</p>` : ''}
                </div>
            </div>
            
            <div class="order-footer">
                <button class="btn btn-outline-sm" onclick="viewOrderDetails(${order.id})">
                    View Details
                </button>
                ${order.status === 'pending' ? `
                    <button class="btn btn-danger-outline-sm" onclick="cancelOrder(${order.id})">
                        Cancel Order
                    </button>
                ` : ''}
            </div>
        </div>
    `).join('');
}

// Filter orders by status
function filterOrders(status) {
    currentFilter = status;
    
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-status') === status) {
            btn.classList.add('active');
        }
    });
    
    // Filter and display
    let filteredOrders = allOrders;
    if (status !== 'all') {
        filteredOrders = allOrders.filter(order => order.status === status);
    }
    
    displayOrders(filteredOrders);
}

// View order details
async function viewOrderDetails(orderId) {
    const modal = document.getElementById('orderModal');
    const modalContent = document.getElementById('orderModalContent');
    
    modal.style.display = 'flex';
    modalContent.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';
    
    try {
        const response = await fetch(`/api/orders/${orderId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const order = data.order;
            modalContent.innerHTML = `
                <div class="order-details">
                    <div class="order-details-header">
                        <div>
                            <h2>Order #${order.id}</h2>
                            <p class="order-date">${formatDateTime(order.created_at)}</p>
                        </div>
                        <div>
                            <span class="status-badge status-${order.status}">${formatStatus(order.status)}</span>
                        </div>
                    </div>
                    
                    <div class="order-details-section">
                        <h3>Order Status Timeline</h3>
                        <div class="status-timeline">
                            <div class="timeline-item ${['pending', 'confirmed', 'preparing', 'ready', 'delivering', 'completed'].includes(order.status) ? 'completed' : ''}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <strong>Order Placed</strong>
                                    <p>${formatDateTime(order.created_at)}</p>
                                </div>
                            </div>
                            <div class="timeline-item ${['confirmed', 'preparing', 'ready', 'delivering', 'completed'].includes(order.status) ? 'completed' : ''}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <strong>Confirmed</strong>
                                    <p>${order.status === 'confirmed' || ['preparing', 'ready', 'delivering', 'completed'].includes(order.status) ? 'Order confirmed by restaurant' : 'Waiting for confirmation'}</p>
                                </div>
                            </div>
                            <div class="timeline-item ${['preparing', 'ready', 'delivering', 'completed'].includes(order.status) ? 'completed' : ''}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <strong>Preparing</strong>
                                    <p>${['preparing', 'ready', 'delivering', 'completed'].includes(order.status) ? 'Your food is being prepared' : 'Not yet started'}</p>
                                </div>
                            </div>
                            <div class="timeline-item ${['delivering', 'completed'].includes(order.status) ? 'completed' : ''}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <strong>Out for Delivery</strong>
                                    <p>${['delivering', 'completed'].includes(order.status) ? 'On the way to you' : 'Not yet dispatched'}</p>
                                </div>
                            </div>
                            <div class="timeline-item ${order.status === 'completed' ? 'completed' : ''}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <strong>Delivered</strong>
                                    <p>${order.status === 'completed' ? 'Order delivered successfully' : 'Pending'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-details-section">
                        <h3>Delivery Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Address:</strong>
                                <p>${order.delivery_address}</p>
                            </div>
                            <div class="info-item">
                                <strong>Phone:</strong>
                                <p>${order.phone}</p>
                            </div>
                            <div class="info-item">
                                <strong>Restaurant:</strong>
                                <p>${order.restaurant.name}</p>
                            </div>
                            ${order.notes ? `
                                <div class="info-item">
                                    <strong>Notes:</strong>
                                    <p>${order.notes}</p>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    
                    <div class="order-details-section">
                        <h3>Order Items</h3>
                        <div class="order-items-list">
                            ${order.items.map(item => `
                                <div class="order-item-detail">
                                    <div class="item-info">
                                        <h4>${item.menu_item.name}</h4>
                                        ${item.special_instructions ? `<p class="item-instructions">Note: ${item.special_instructions}</p>` : ''}
                                    </div>
                                    <div class="item-quantity">x${item.quantity}</div>
                                    <div class="item-price">TZS ${formatPrice(item.unit_price)}</div>
                                    <div class="item-subtotal">TZS ${formatPrice(item.subtotal)}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div class="order-details-section">
                        <h3>Payment Summary</h3>
                        <div class="payment-summary">
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
                            <div class="payment-info">
                                <strong>Payment Method:</strong> ${formatPaymentMethod(order.payment_method)}
                                <br>
                                <strong>Payment Status:</strong> <span class="payment-badge payment-${order.payment_status}">${formatPaymentStatus(order.payment_status)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    } catch (error) {
        modalContent.innerHTML = '<p class="error-message">Failed to load order details.</p>';
        console.error('Error:', error);
    }
}

// Close order modal
function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
}

// Cancel order (optional feature)
async function cancelOrder(orderId) {
    if (!confirm('Are you sure you want to cancel this order?')) {
        return;
    }
    
    // This would need a backend endpoint
    alert('Order cancellation feature coming soon!');
}

// Format helpers
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
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

function formatPrice(price) {
    return parseFloat(price).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}
</script>
@endpush