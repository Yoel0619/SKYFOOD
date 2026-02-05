@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container">
    <h1 class="mb-4">Shopping Cart</h1>

    @if($cartItems->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    @foreach($cartItems as $item)
                                    <tr data-cart-id="{{ $item->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->food->image)
                                                    <img src="{{ asset('storage/' . $item->food->image) }}" alt="{{ $item->food->name }}" class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                        <i class="fas fa-image text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $item->food->name }}</h6>
                                                    <small class="text-muted">{{ $item->food->category->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="item-price">TSh {{ number_format($item->food->final_price, 0) }}</td>
                                        <td>
                                            <div class="input-group" style="max-width: 130px;">
                                                <button class="btn btn-sm btn-outline-secondary decrease-qty" type="button">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control form-control-sm text-center cart-quantity" value="{{ $item->quantity }}" min="1" max="10" data-cart-id="{{ $item->id }}" readonly>
                                                <button class="btn btn-sm btn-outline-secondary increase-qty" type="button">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="item-subtotal fw-bold">TSh {{ number_format($item->subtotal, 0) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-danger remove-from-cart" data-cart-id="{{ $item->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('menu.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                    <button class="btn btn-outline-danger" id="clearCart">
                        <i class="fas fa-trash"></i> Clear Cart
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal">TSh {{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (16%):</span>
                            <span id="cart-tax">TSh {{ number_format($tax, 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Delivery Fee:</span>
                            <span id="cart-delivery">TSh {{ number_format($deliveryFee, 0) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary" id="cart-total">TSh {{ number_format($total, 0) }}</strong>
                        </div>
                        <a href="{{ route('orders.checkout') }}" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-check-circle"></i> Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Add some delicious food to your cart!</p>
            <a href="{{ route('menu.index') }}" class="btn btn-primary">
                <i class="fas fa-book-open"></i> Browse Menu
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/cart.js') }}"></script>
@endpush