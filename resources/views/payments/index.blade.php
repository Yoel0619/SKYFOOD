@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-money-bill-wave"></i> Payments Management</h1>
            <p>Manage all payment transactions</p>
        </div>
        <a href="{{ route('payments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Record Payment
        </a>
    </div>
    
    <!-- Search & Filter -->
    <div class="filter-bar">
        <input 
            type="text" 
            class="form-control" 
            id="searchInput" 
            placeholder="🔍 Search by transaction ID..."
            value="{{ request('search') }}"
        >
        
        <select class="form-control" id="statusFilter">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
        </select>
        
        <select class="form-control" id="methodFilter">
            <option value="">All Methods</option>
            <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="mpesa" {{ request('method') == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
            <option value="card" {{ request('method') == 'card' ? 'selected' : '' }}>Card</option>
            <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
        </select>
        
        <button class="btn btn-secondary" onclick="applyFilters()">
            <i class="fas fa-filter"></i> Filter
        </button>
        
        <button class="btn btn-outline" onclick="clearFilters()">
            <i class="fas fa-times"></i> Clear
        </button>
    </div>
    
    @if(isset($payments) && $payments->count() > 0)
        <div class="card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td><strong>{{ $payment->transaction_id ?? 'N/A' }}</strong></td>
                            <td>{{ $payment->order->order_number }}</td>
                            <td>{{ $payment->order->user->name }}</td>
                            <td>
                                @if($payment->payment_method == 'cash')
                                    <span class="badge" style="background: #2ecc71;">💵 Cash</span>
                                @elseif($payment->payment_method == 'mpesa')
                                    <span class="badge" style="background: #27ae60;">📱 M-Pesa</span>
                                @elseif($payment->payment_method == 'card')
                                    <span class="badge" style="background: #3498db;">💳 Card</span>
                                @else
                                    <span class="badge" style="background: #9b59b6;">🏦 Bank</span>
                                @endif
                            </td>
                            <td><strong>TZS {{ number_format($payment->amount, 0) }}</strong></td>
                            <td>
                                @if($payment->status == 'pending')
                                    <span class="badge badge-warning">⏳ Pending</span>
                                @elseif($payment->status == 'completed')
                                    <span class="badge badge-success">✅ Completed</span>
                                @elseif($payment->status == 'failed')
                                    <span class="badge badge-danger">❌ Failed</span>
                                @else
                                    <span class="badge" style="background: #95a5a6;">🔄 Refunded</span>
                                @endif
                            </td>
                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button 
    class="btn btn-sm btn-danger" 
    onclick="deleteItem('/payments/{{ $payment->id }}', 'Delete this payment?')"
>
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
                {{ $payments->links() }}
            </div>
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <div style="font-size: 80px; margin-bottom: 1rem;">💳</div>
                <h3>No Payments Yet</h3>
                <p>Payment records will appear here once customers make payments</p>
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Record First Payment
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
    const method = document.getElementById('methodFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (method) params.append('method', method);
    
    window.location.href = '{{ route("payments.index") }}?' + params.toString();
}

function clearFilters() {
    window.location.href = '{{ route("payments.index") }}';
}

async function deletePayment(id) {
    if (!confirm('Delete this payment record?')) return;
    
    try {
        const response = await fetchAPI(`/payments/${id}`, { method: 'DELETE' });
        
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