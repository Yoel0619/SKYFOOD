@extends('layouts.app')

@section('title', 'Roles Management')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-user-tag"></i> Roles Management</h1>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Role
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>All Roles</h2>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Users Count</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>
                            <strong>{{ ucfirst($role->name) }}</strong>
                            @if(in_array($role->name, ['admin', 'customer']))
                                <span class="badge badge-primary">System</span>
                            @endif
                        </td>
                        <td>{{ $role->description ?? 'No description' }}</td>
                        <td>{{ $role->users_count }} users</td>
                        <td>{{ $role->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('roles.show', $role->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @if(!in_array($role->name, ['admin', 'customer']))
                                <button 
                                    class="btn btn-sm btn-danger" 
                                    onclick="deleteRole({{ $role->id }})"
                                >
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">
                            <p>No roles found</p>
                            <a href="{{ route('roles.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                                Create First Role
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination-container">
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function deleteRole(roleId) {
   @if(!in_array($role->name, ['admin', 'customer']))
<button 
    class="btn btn-sm btn-danger" 
    onclick="deleteItem('/roles/{{ $role->id }}', 'Delete {{ $role->name }}?')"
>
    <i class="fas fa-trash"></i> Delete
</button>
@endif
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete role', 'error');
    }
}
</script>
@endpush