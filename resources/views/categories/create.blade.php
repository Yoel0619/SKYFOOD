@extends('layouts.app')

@section('title', 'Add New Category')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Add New Category</h1>
        <a href="{{ route('categories.index') }}" class="btn btn-outline">
            ← Back to Categories
        </a>
    </div>
    
    <div class="form-container">
        <form id="categoryForm" action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="name">Category Name *</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="name" 
                    name="name" 
                    placeholder="e.g., Pizza, Burgers, Drinks"
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
                    placeholder="Category description..."
                ></textarea>
            </div>
            
            <div class="form-group">
                <label for="status">Status *</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="image">Category Image</label>
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
                    ✅ Create Category
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline">
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
</script>
@endpush