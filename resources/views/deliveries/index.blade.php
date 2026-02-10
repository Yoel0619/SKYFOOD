@extends('layouts.app')

@section('title', 'Deliveries')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-truck"></i> Deliveries Management</h1>
            <p>Track and manage all deliveries</p>
        </div>
        <a href="{{ route('deliveries.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create Delivery
        </a>
    </div>
    
    <!-- Search & Filter -->
    <div class="filter-bar">
        <input 
            type="text" 
            class="form-control" 
            id="searchInput" 
            placeholder="🔍 Search by tracking number..."
            value="{{ request('search') }}"
        >
        
        <select class="form-control" id="statusFilter">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
            <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
            <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        
        <button class="btn btn-secondary" onclick="applyFilters()">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <button class="btn btn-outline" onclick="clearFilters()">
            <i class="fas fa-times"></i> Clear
        </button>
    </div>
    
    @if(isset($deliveries) && $deliveries->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tracking #</th>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Delivery Person</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                        <tr>
                            <td><strong>{{ $delivery->tracking_number }}</strong></td>
                            <td>{{ $delivery->order->order_number }}</td>
                            <td>{{ $delivery->order->user->name }}</td>
                            <td>{{ $delivery->deliveryPerson->name ?? 'Not assigned' }}</td>
                            <td>{{ Str::limit($delivery->delivery_address, 30) }}</td>
                            <td>
                                @if($delivery->status == 'pending')
                                    <span class="badge badge-warning">⏳ Pending</span>
                                @elseif($delivery->status == 'assigned')
                                    <span class="badge" style="background: #3498db;">👤 Assigned</span>
                                @elseif($delivery->status == 'picked_up')
                                    <span class="badge" style="background: #9b59b6;">📦 Picked Up</span>
                                @elseif($delivery->status == 'in_transit')
                                    <span class="badge" style="background: #f39c12;">🚚 In Transit</span>
                                @elseif($delivery->status == 'delivered')
                                    <span class="badge badge-success">✅ Delivered</span>
                                @else
                                    <span class="badge badge-danger">❌ Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('deliveries.show', $delivery->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('deliveries.edit', $delivery->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="deleteDelivery({{ $delivery->id }})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-container">
                {{ $deliveries->links() }}
            </div>
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <div style="font-size: 80px; margin-bottom: 1rem;">🚚</div>
                <h3>No Deliveries Yet</h3>
                <p>Delivery records will appear here once orders are placed</p>
                <a href="{{ route('deliveries.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Delivery
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("deliveries.index") }}?' + params.toString();
}

function clearFilters() {
    window.location.href = '{{ route("deliveries.index") }}';
}

async function deleteDelivery(id) {
    if (!confirm('Delete this delivery?')) return;
    
    try {
        const response = await fetchAPI(`/deliveries/${id}`, { method: 'DELETE' });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete', 'error');
    }
}
</script>
@endpush