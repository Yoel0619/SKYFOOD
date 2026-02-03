@extends('layouts.app')

@section('title', 'Admin Dashboard - SKYFOOD')

@section('content')
<div class="admin-dashboard">
    <div class="container-fluid">
        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <p>Welcome back, {{ Auth::user()->name }}</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #667eea;">📊</div>
                <div class="stat-content">
                    <h3 id="totalOrders">0</h3>
                    <p>Total Orders</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #f093fb;">⏳</div>
                <div class="stat-content">
                    <h3 id="pendingOrders">0</h3>
                    <p>Pending Orders</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #4facfe;">💰</div>
                <div class="stat-content">
                    <h3 id="totalRevenue">TZS 0</h3>
                    <p>Total Revenue</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #43e97b;">👥</div>
                <div class="stat-content">
                    <h3 id="totalCustomers">0</h3>
                    <p>Total Customers</p>
                </div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="dashboard-content">
            <!-- Recent Orders -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Recent Orders</h2>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-sm">View All</a>
                </div>
                <div class="card-body">
                    <div id="loadingRecentOrders" class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                    <div id="recentOrdersTable" style="display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Restaurant</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="recentOrdersBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Orders by Status -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Orders by Status</h2>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" height="100"></canvas>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Revenue (Last 7 Days)</h2>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>

            <!-- Top Selling Items -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2>Top Selling Items</h2>
                </div>
                <div class="card-body">
                    <div id="topItemsList" class="top-items-list">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

async function loadDashboardData() {
    try {
        const response = await fetch('/admin/api/dashboard/stats', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            updateStats(data.stats);
            displayRecentOrders(data.recent_orders);
            createOrderStatusChart(data.orders_by_status);
            createRevenueChart(data.revenue_by_day);
            displayTopItems(data.top_items);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function updateStats(stats) {
    document.getElementById('totalOrders').textContent = stats.total_orders;
    document.getElementById('pendingOrders').textContent = stats.pending_orders;
    document.getElementById('totalRevenue').textContent = `TZS ${formatPrice(stats.total_revenue)}`;
    document.getElementById('totalCustomers').textContent = stats.total_customers;
}

function displayRecentOrders(orders) {
    const loadingEl = document.getElementById('loadingRecentOrders');
    const tableEl = document.getElementById('recentOrdersTable');
    const bodyEl = document.getElementById('recentOrdersBody');
    
    loadingEl.style.display = 'none';
    tableEl.style.display = 'block';
    
    bodyEl.innerHTML = orders.map(order => `
        <tr>
            <td>#${order.id}</td>
            <td>${order.user.name}</td>
            <td>${order.restaurant.name}</td>
            <td>TZS ${formatPrice(order.total_amount)}</td>
            <td><span class="status-badge status-${order.status}">${formatStatus(order.status)}</span></td>
            <td>${formatDateTime(order.created_at)}</td>
            <td>
                <a href="{{ route('admin.orders.index') }}?order=${order.id}" class="btn btn-sm btn-outline">View</a>
            </td>
        </tr>
    `).join('');
}

function createOrderStatusChart(ordersByStatus) {
    const ctx = document.getElementById('orderStatusChart').getContext('2d');
    
    const labels = Object.keys(ordersByStatus);
    const data = Object.values(ordersByStatus);
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels.map(l => formatStatus(l)),
            datasets: [{
                data: data,
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#f093fb',
                    '#4facfe',
                    '#43e97b',
                    '#fa709a',
                    '#ff6b6b'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function createRevenueChart(revenueData) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    const labels = revenueData.map(d => formatDate(d.date));
    const data = revenueData.map(d => d.revenue);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (TZS)',
                data: data,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'TZS ' + formatPrice(value);
                        }
                    }
                }
            }
        }
    });
}

function displayTopItems(items) {
    const listEl = document.getElementById('topItemsList');
    
    listEl.innerHTML = items.map((item, index) => `
        <div class="top-item">
            <div class="item-rank">#${index + 1}</div>
            <div class="item-details">
                <h4>${item.name}</h4>
                <p>${item.total_sold} sold · TZS ${formatPrice(item.total_revenue)}</p>
            </div>
        </div>
    `).join('');
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

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short'
    });
}

function formatPrice(price) {
    return parseFloat(price).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}
</script>
@endpush