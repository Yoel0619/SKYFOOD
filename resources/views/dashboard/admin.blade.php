@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>SKYFOOD</h1>
        <p>Welcome back, {{ auth()->user()->name }}!</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e3f2fd;">
                <span>👥</span>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_users'] }}</h3>
                <p>Total Users</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #f3e5f5;">
                <span>🍔</span>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_products'] }}</h3>
                <p>Total Products</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff3e0;">
                <span>📦</span>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_orders'] }}</h3>
                <p>Total Orders</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #e8f5e9;">
                <span>💰</span>
            </div>
            <div class="stat-content">
                <h3>TZS {{ number_format($stats['total_revenue'], 0) }}</h3>
                <p>Total Revenue</p>
            </div>
        </div>
    </div>
    
    <!-- Order Stats -->
    <div class="grid grid-3">
        <div class="card">
            <h3 style="color: #f39c12;">⏳ Pending Orders</h3>
            <h2 style="font-size: 2.5rem; margin-top: 1rem;">{{ $stats['pending_orders'] }}</h2>
        </div>
        
        <div class="card">
            <h3 style="color: #3498db;">🔄 Processing</h3>
            <h2 style="font-size: 2.5rem; margin-top: 1rem;">
                {{ $stats['total_orders'] - $stats['pending_orders'] - $stats['completed_orders'] }}
            </h2>
        </div>
        
        <div class="card">
            <h3 style="color: #2ecc71;">✅ Completed</h3>
            <h2 style="font-size: 2.5rem; margin-top: 1rem;">{{ $stats['completed_orders'] }}</h2>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2>Quick Actions</h2>
        </div>
        <div class="quick-actions">
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                ➕ Add Product
            </a>
            <a href="{{ route('categories.create') }}" class="btn btn-secondary">
                📁 Add Category
            </a>
            <a href="{{ route('users.create') }}" class="btn btn-success">
                👤 Add User
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-warning">
                📋 View All Orders
            </a>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h2>Recent Orders</h2>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ $order->orderItems->count() }} items</td>
                        <td>TZS {{ number_format($order->total_amount, 0) }}</td>
                        <td>
                            @if($order->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($order->status == 'processing')
                                <span class="badge badge-primary">Processing</span>
                            @elseif($order->status == 'completed')
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            No orders yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .page-header {
        margin-bottom: 2rem;
    }
    
    .page-header h1 {
        font-size: 2rem;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }
    
    .page-header p {
        color: #636e72;
    }
    
    .quick-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endpush