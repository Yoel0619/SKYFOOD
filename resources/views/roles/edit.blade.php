@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-edit"></i> Edit Role</h1>
        <a href="{{ route('roles.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>
    
    <div class="form-container">
        <form id="roleForm" action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name"><i class="fas fa-tag"></i> Role Name *</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="name" 
                    name="name" 
                    value="{{ $role->name }}"
                    {{ in_array($role->name, ['admin', 'customer']) ? 'readonly' : '' }}
                    required
                >
                @if(in_array($role->name, ['admin', 'customer']))
                    <small style="color: #f39c12;">System role - name cannot be changed</small>
                @endif
            </div>
            
            <div class="form-group">
                <label for="description"><i class="fas fa-align-left"></i> Description</label>
                <textarea 
                    class="form-control" 
                    id="description" 
                    name="description" 
                    rows="4"
                >{{ $role->description }}</textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Role
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Cancel
                </a>
                @if(!in_array($role->name, ['admin', 'customer']))
                <button type="button" class="btn btn-danger" onclick="deleteRole({{ $role->id }})">
                    <i class="fas fa-trash"></i> Delete Role
                </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, .08);
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
handleFormSubmit('roleForm');

async function deleteRole(roleId) {
    if (!confirmDelete('Are you sure you want to delete this role?')) return;
    
    try {
        const response = await fetchAPI(`/roles/${roleId}`, {
            method: 'DELETE'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => {
                window.location.href = '{{ route("roles.index") }}';
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete role', 'error');
    }
}
</script>
@endpush