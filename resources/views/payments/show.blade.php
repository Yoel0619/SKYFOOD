@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-money-bill-wave"></i> Payment Details</h1>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('payments.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
    
    <div class="payment-detail">
        <div class="card">
            <div class="card-header">
                <h2>Payment Information</h2>
                @if($payment->status == 'pending')
                    <span class="badge badge-warning">⏳ Pending</span>
                @elseif($payment->status == 'completed')
                    <span class="badge badge-success">✅ Completed</span>
                @elseif($payment->status == 'failed')
                    <span class="badge badge-danger">❌ Failed</span>
                @else
                    <span class="badge" style="background: #95a5a6;">🔄 Refunded</span>
                @endif
            </div>
            
            <div class="payment-info-grid">
                <div class="info-item">
                    <label>Transaction ID</label>
                    <strong>{{ $payment->transaction_id ?? 'N/A' }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Payment Method</label>
                    <strong>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Amount</label>
                    <strong style="color: #fbaf32; font-size: 1.5rem;">TZS {{ number_format($payment->amount, 0) }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Payment Date</label>
                    <strong>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : 'Not paid yet' }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Created</label>
                    <strong>{{ $payment->created_at->format('M d, Y H:i') }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Last Updated</label>
                    <strong>{{ $payment->updated_at->format('M d, Y H:i') }}</strong>
                </div>
            </div>
            
            @if($payment->payment_details)
            <div class="payment-details-section">
                <h3>Additional Details</h3>
                <p>{{ $payment->payment_details }}</p>
            </div>
            @endif
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Order Information</h2>
                <a href="{{ route('orders.show', $payment->order->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> View Full Order
                </a>
            </div>
            
            <div class="payment-info-grid">
                <div class="info-item">
                    <label>Order Number</label>
                    <strong>{{ $payment->order->order_number }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Customer</label>
                    <strong>{{ $payment->order->user->name }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Customer Email</label>
                    <strong>{{ $payment->order->user->email }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Order Total</label>
                    <strong>TZS {{ number_format($payment->order->total_amount, 0) }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Order Status</label>
                    <strong>{{ ucfirst($payment->order->status) }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Order Date</label>
                    <strong>{{ $payment->order->created_at->format('M d, Y H:i') }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .payment-detail {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .payment-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        padding: 1.5rem;
    }
    
    .info-item label {
        display: block;
        font-size: 0.875rem;
        color: #757575;
        margin-bottom: 0.25rem;
    }
    
    .info-item strong {
        font-size: 1.1rem;
        color: #454545;
    }
    
    .payment-details-section {
        margin-top: 1.5rem;
        padding: 1.5rem;
        border-top: 2px solid #f8f9fa;
    }
    
    .payment-details-section h3 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .payment-details-section p {
        color: #757575;
    }
    
    @media (max-width: 768px) {
        .payment-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush