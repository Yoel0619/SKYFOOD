@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-user-tag"></i> Role Details</h1>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('roles.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Roles
            </a>
            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
    
    <div class="role-detail">
        <div class="card">
            <div class="card-header">
                <h2>Role Information</h2>
                @if(in_array($role->name, ['admin', 'customer']))
                    <span class="badge badge-primary">System Role</span>
                @endif
            </div>
            
            <div class="role-info-grid">
                <div class="info-item">
                    <label>Role ID</label>
                    <strong>#{{ $role->id }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Role Name</label>
                    <strong>{{ ucfirst($role->name) }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Description</label>
                    <strong>{{ $role->description ?? 'No description' }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Total Users</label>
                    <strong>{{ $role->users->count() }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Created</label>
                    <strong>{{ $role->created_at->format('M d, Y H:i') }}</strong>
                </div>
                
                <div class="info-item">
                    <label>Last Updated</label>
                    <strong>{{ $role->updated_at->format('M d, Y H:i') }}</strong>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Users with this Role ({{ $role->users->count() }})</h2>
            </div>
            
            @if($role->users->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->users as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>
                                    @if($user->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <p>No users with this role yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .role-detail {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .role-info-grid {
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
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #757575;
    }
    
    @media (max-width: 768px) {
        .role-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush