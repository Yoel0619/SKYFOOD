@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>User Details</h1>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('users.index') }}" class="btn btn-outline">
                ← Back to Users
            </a>
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                ✏️ Edit
            </a>
        </div>
    </div>
    
    <div class="user-detail">
        <div class="card">
            <div class="card-header">
                <h2>User Information</h2>
                @if($user->status == 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">Inactive</span>
                @endif
            </div>
            
            <div class="user-info-grid">
                <div class="info-item">
                    <label>Full Name</label>
                    <strong>{{ $user->name }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Email Address</label>
                    <strong>{{ $user->email }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Phone Number</label>
                    <strong>{{ $user->phone }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Role</label>
                    <span class="badge badge-primary">{{ ucfirst($user->role->name) }}</span>
                </div>
                
                <div class="info-item">
                    <label>Address</label>
                    <strong>{{ $user->address }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Member Since</label>
                    <strong>{{ $user->created_at->format('M d, Y H:i') }}</strong>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Order History ({{ $user->orders->count() }})</h2>
            </div>
            
            @if($user->orders->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <p>No orders yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .user-detail {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .user-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        padding: 1.5rem;
    }
    
    .info-item label {
        display: block;
        font-size: 0.875rem;
        color: #636e72;
        margin-bottom: 0.25rem;
    }
    
    .info-item strong {
        font-size: 1.1rem;
        color: var(--dark-color);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #636e72;
    }
    
    @media (max-width: 768px) {
        .user-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush