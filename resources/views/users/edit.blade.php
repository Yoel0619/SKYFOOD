@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Edit User</h1>
        <a href="{{ route('users.index') }}" class="btn btn-outline">
            ← Back to Users
        </a>
    </div>
    
    <div class="form-container">
        <form id="userForm" action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="name" 
                        name="name" 
                        value="{{ $user->name }}"
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
                        value="{{ $user->email }}"
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
                        value="{{ $user->phone }}"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="role_id">Role *</label>
                    <select class="form-control" id="role_id" name="role_id" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
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
                    required
                >{{ $user->address }}</textarea>
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="password">New Password (Optional)</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="Leave empty to keep current"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="Re-enter new password"
                    >
                </div>
            </div>
            
            <div class="form-group">
                <label for="status">Status *</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    💾 Update User
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline">
                    Cancel
                </a>
                <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }})">
                    🗑️ Delete User
                </button>
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

async function deleteUser(userId) {
    if (!confirmDelete('Are you sure you want to delete this user?')) return;
    
    try {
        const response = await fetchAPI(`/users/${userId}`, {
            method: 'DELETE'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => {
                window.location.href = '{{ route("users.index") }}';
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete user', 'error');
    }
}
</script>
@endpush