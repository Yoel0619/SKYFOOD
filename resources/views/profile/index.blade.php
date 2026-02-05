@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-user"></i> My Profile</h1>

    <div class="row">
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($user->profile_image)
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <i class="fas fa-user-circle fa-10x text-muted"></i>
                        @endif
                    </div>
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Orders:</span>
                        <strong>{{ $user->orders->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Completed:</span>
                        <strong class="text-success">{{ $user->orders->where('order_status', 'delivered')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Saved Addresses:</span>
                        <strong>{{ $addresses->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Update Profile -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Update Profile</h5>
                </div>
                <div class="card-body">
                    <form id="updateProfileForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" value="{{ $user->phone }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" name="profile_image" accept="image/*">
                            <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="updateProfileBtn">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-lock"></i> Change Password</h5>
                </div>
                <div class="card-body">
                    <form id="changePasswordForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" required>
                            <small class="text-muted">Min 8 characters, must contain uppercase, lowercase, and numbers</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="new_password_confirmation" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Saved Addresses -->
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Saved Addresses</h5>
                    <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                        <i class="fas fa-plus"></i> Add New
                    </button>
                </div>
                <div class="card-body" id="addressesList">
                    @if($addresses->count() > 0)
                        @foreach($addresses as $address)
                            <div class="card mb-3" data-address-id="{{ $address->id }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                {{ $address->label }}
                                                @if($address->is_default)
                                                    <span class="badge bg-success">Default</span>
                                                @endif
                                            </h6>
                                            <p class="mb-0 text-muted">{{ $address->full_address }}</p>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary edit-address" 
                                                    data-address-id="{{ $address->id }}"
                                                    data-label="{{ $address->label }}"
                                                    data-street="{{ $address->street_address }}"
                                                    data-city="{{ $address->city }}"
                                                    data-state="{{ $address->state }}"
                                                    data-postal="{{ $address->postal_code }}"
                                                    data-default="{{ $address->is_default }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-address" data-address-id="{{ $address->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center py-3">No saved addresses yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addressModalTitle">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addressForm">
                    @csrf
                    <input type="hidden" id="address_id" name="address_id">
                    <div class="mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-control" id="address_label" name="label" placeholder="e.g., Home, Office" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="address_street" name="street_address" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" id="address_city" name="city" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">State/Region</label>
                            <input type="text" class="form-control" id="address_state" name="state" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Postal Code</label>
                        <input type="text" class="form-control" id="address_postal" name="postal_code">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="address_default" name="is_default" value="1">
                        <label class="form-check-label" for="address_default">
                            Set as default address
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAddressBtn">Save Address</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/profile.js') }}"></script>
@endpush