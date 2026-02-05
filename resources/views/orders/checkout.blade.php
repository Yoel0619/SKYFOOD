@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container">
    <h1 class="mb-4">Checkout</h1>

    <form id="checkoutForm">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Delivery Address -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Delivery Address</h5>
                        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                    <div class="card-body">
                        @if($addresses->count() > 0)
                            <div class="row">
                                @foreach($addresses as $address)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="delivery_address_id" id="address{{ $address->id }}" value="{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }} required>
                                        <label class="form-check-label w-100" for="address{{ $address->id }}">
                                            <div class="border rounded p-3">
                                                <strong>{{ $address->label }}</strong>
                                                @if($address->is_default)
                                                    <span class="badge bg-success">Default</span>
                                                @endif
                                                <p class="mb-0 mt-2 small">
                                                    {{ $address->street_address }}<br>
                                                    {{ $address->city }}, {{ $address->state }}<br>
                                                    @if($address->postal_code)
                                                        {{ $address->postal_code }}
                                                    @endif
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Please add a delivery address to continue.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                            <label class="form-check-label" for="cash">
                                <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                            <label class="form-check-label" for="card">
                                <i class="fas fa-credit-card"></i> Credit/Debit Card
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="mobile_money" value="mobile_money">
                            <label class="form-check-label" for="mobile_money">
                                <i class="fas fa-mobile-alt"></i> Mobile Money (M-Pesa, Tigo Pesa, Airtel Money)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Special Instructions -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Special Instructions</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="special_instructions" rows="3" placeholder="Any special requests? (e.g., no onions, extra spicy, etc.)"></textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card mb-4 sticky-top" style="top: 80px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Items ({{ $cartItems->count() }})</strong>
                        </div>
                        
                        @foreach($cartItems as $item)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>{{ $item->quantity }}x {{ Str::limit($item->food->name, 20) }}</span>
                            <span>TSh {{ number_format($item->subtotal, 0) }}</span>
                        </div>
                        @endforeach
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>TSh {{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (16%):</span>
                            <span>TSh {{ number_format($tax, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Delivery Fee:</span>
                            <span>TSh {{ number_format($deliveryFee, 0) }}</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary">TSh {{ number_format($total, 0) }}</strong>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg" id="placeOrderBtn" {{ $addresses->count() == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-check-circle"></i> Place Order
                        </button>
                        
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left"></i> Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Delivery Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addAddressForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Address Label</label>
                        <input type="text" class="form-control" name="label" placeholder="e.g., Home, Office" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Street Address</label>
                        <input type="text" class="form-control" name="street_address" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">State/Region</label>
                            <input type="text" class="form-control" name="state" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Postal Code (Optional)</label>
                        <input type="text" class="form-control" name="postal_code">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="setDefault">
                        <label class="form-check-label" for="setDefault">
                            Set as default address
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/checkout.js') }}"></script>
@endpush