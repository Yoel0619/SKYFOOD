@extends('layouts.app')

@section('title', 'Users Management')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Users Management</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            ➕ Add User
        </a>
    </div>
    
    <!-- Search & Filter -->
    <div class="filter-bar">
        <input 
            type="text" 
            class="form-control" 
            id="searchInput" 
            placeholder="🔍 Search users..."
            value="{{ request('search') }}"
        >
        
        <select class="form-control" id="roleFilter">
            <option value="">All Roles</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
        
        <select class="form-control" id="statusFilter">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        
        <button class="btn btn-secondary" onclick="applyFilters()">
            Filter
        </button>
        
        <button class="btn btn-outline" onclick="clearFilters()">
            Clear
        </button>
    </div>
    
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            <span class="badge badge-primary">{{ ucfirst($user->role->name) }}</span>
                        </td>
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
                                View
                            </a>
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>
                            <button 
                                class="btn btn-sm btn-secondary" 
                                onclick="toggleStatus({{ $user->id }})"
                            >
                                Toggle Status
                            </button>
                            <button 
                                class="btn btn-sm btn-danger" 
                                onclick="deleteUser({{ $user->id }})"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            No users found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination-container">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const role = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (role) params.append('role', role);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("users.index") }}?' + params.toString();
}

function clearFilters() {
    window.location.href = '{{ route("users.index") }}';
}

async function toggleStatus(userId) {
    try {
        const response = await fetchAPI(`/users/${userId}/toggle-status`, {
            method: 'POST'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to toggle status', 'error');
    }
}

async function deleteUser(userId) {
    if (!confirmDelete('Are you sure you want to delete this user?')) return;
    
    try {
        const response = await fetchAPI(`/users/${userId}`, {
            method: 'DELETE'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete user', 'error');
    }
}
</script>
@endpush