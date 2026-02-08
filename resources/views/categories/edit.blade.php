@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Edit Category</h1>
        <a href="{{ route('categories.index') }}" class="btn btn-outline">
            ← Back to Categories
        </a>
    </div>
    
    <div class="form-container">
        <form id="categoryForm" action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name">Category Name *</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="name" 
                    name="name" 
                    value="{{ $category->name }}"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea 
                    class="form-control" 
                    id="description" 
                    name="description" 
                    rows="4"
                >{{ $category->description }}</textarea>
            </div>
            
            <div class="form-group">
                <label for="status">Status *</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active" {{ $category->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $category->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Current Image</label>
                @if($category->image)
                    <div class="current-image">
                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}">
                    </div>
                @else
                    <p style="color: #636e72;">No image uploaded</p>
                @endif
            </div>
            
            <div class="form-group">
                <label for="image">Change Image (Optional)</label>
                <input 
                    type="file" 
                    class="form-control" 
                    id="image" 
                    name="image" 
                    accept="image/*"
                    onchange="previewImage(this, 'imagePreview')"
                >
            </div>
            
            <div class="image-preview-container">
                <img id="imagePreview" src="" alt="Preview" style="display: none;">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    💾 Update Category
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline">
                    Cancel
                </a>
                <button type="button" class="btn btn-danger" onclick="deleteCategory({{ $category->id }})">
                    🗑️ Delete
                </button>
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
    
    .current-image img {
        max-width: 300px;
        border-radius: 8px;
        box-shadow: var(--shadow);
        margin-top: 0.5rem;
    }
    
    .image-preview-container {
        margin-top: 1rem;
    }
    
    .image-preview-container img {
        max-width: 300px;
        border-radius: 8px;
        box-shadow: var(--shadow);
    }
</style>
@endpush

@push('scripts')
<script>
handleFormSubmit('categoryForm');

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
                window.location.href = '{{ route("categories.index") }}';
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete category', 'error');
    }
}
</script>
@endpush