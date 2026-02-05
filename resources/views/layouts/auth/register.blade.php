@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-primary"></i>
                        <h3 class="mt-2">Create Account</h3>
                        <p class="text-muted">Join us today!</p>
                    </div>

                    <form id="registerForm">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="+255XXXXXXXXX" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Min 8 characters, must contain uppercase, lowercase, and numbers</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="registerBtn">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </form>

                    <hr>
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? <a href="{{ route('login') }}">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/auth.js') }}"></script>
@endpush