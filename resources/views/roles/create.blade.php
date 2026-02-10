@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-plus"></i> Create New Role</h1>
        <a href="{{ route('roles.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>
    
    <div class="form-container">
        <form id="roleForm" action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name"><i class="fas fa-tag"></i> Role Name *</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="name" 
                    name="name" 
                    placeholder="e.g., manager, delivery, staff"
                    required
                >
                <small style="color: #757575;">Use lowercase, no spaces (e.g., delivery_driver)</small>
            </div>
            
            <div class="form-group">
                <label for="description"><i class="fas fa-align-left"></i> Description</label>
                <textarea 
                    class="form-control" 
                    id="description" 
                    name="description" 
                    rows="4" 
                    placeholder="Describe this role's responsibilities..."
                ></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> Create Role
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Cancel
                </a>
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
</script>
@endpush