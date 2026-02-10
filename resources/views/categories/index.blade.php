@extends('layouts.app')

@section('title', 'Categories Management')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Categories</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            ➕ Add Category
        </a>
    </div>
    
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>
                            @if($category->image)
                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            @else
                                <div style="width: 50px; height: 50px; background: var(--light-color); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    📁
                                </div>
                            @endif
                        </td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ Str::limit($category->description, 50) }}</td>
                        <td>{{ $category->products_count }} products</td>
                        <td>
                            @if($category->status == 'active')
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $category->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('categories.show', $category->id) }}" class="btn btn-sm btn-primary">
                                View
                            </a>
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>
                          <div class="action-buttons">
    <a href="{{ route('categories.show', $category->id) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-eye"></i> View
    </a>
    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit"></i> Edit
    </a>
    <button 
        class="btn btn-sm btn-danger" 
        onclick="deleteItem('/categories/{{ $category->id }}', 'Delete {{ $category->name }}?')"
    >
        <i class="fas fa-trash"></i> Delete
    </button>
</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            <p>No categories found</p>
                            <a href="{{ route('categories.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                                Create First Category
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination-container">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function deleteCategory(categoryId) {
    if (!confirmDelete('Are you sure? This will affect all products in this category.')) {
        return;
    }
    
    try {
        const response = await fetchAPI(`/categories/${categoryId}`, {
            method: 'DELETE'
        });
        
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete category', 'error');
    }
}
</script>
@endpush