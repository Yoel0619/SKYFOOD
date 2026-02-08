@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Add New User</h1>
        <a href="{{ route('users.index') }}" class="btn btn-outline">
            ← Back to Users
        </a>
    </div>
    
    <div class="form-container">
        <form id="userForm" action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="name" 
                        name="name" 
                        placeholder="John Doe"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        placeholder="user@example.com"
                        required
                    >
                </div>
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="phone" 
                        name="phone" 
                        placeholder="0712345678"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="role_id">Role *</label>
                    <select class="form-control" id="role_id" name="role_id" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Address *</label>
                <textarea 
                    class="form-control" 
                    id="address" 
                    name="address" 
                    rows="3" 
                    placeholder="Full address"
                    required
                ></textarea>
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="Minimum 8 characters"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password *</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="Re-enter password"
                        required
                    >
                </div>
            </div>
            
            <div class="form-group">
                <label for="status">Status *</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    ✅ Create User
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: var(--shadow);
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid var(--border-color);
    }
</style>
@endpush

@push('scripts')
<script>
handleFormSubmit('userForm');
</script>
@endpush