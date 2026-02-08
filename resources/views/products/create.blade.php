@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Add New Product</h1>
        <a href="{{ route('products.index') }}" class="btn btn-outline">
            ← Back to Products
        </a>
    </div>
    
    <div class="form-container">
        <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="name" 
                        name="name" 
                        placeholder="e.g., Margherita Pizza"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea 
                    class="form-control" 
                    id="description" 
                    name="description" 
                    rows="4" 
                    placeholder="Product description..."
                ></textarea>
            </div>
            
            <div class="grid grid-2">
                <div class="form-group">
                    <label for="price">Price (TZS) *</label>
                    <input 
                        type="number" 
                        class="form-control" 
                        id="price" 
                        name="price" 
                        placeholder="15000"
                        min="0"
                        step="0.01"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock Quantity *</label>
                    <input 
                        type="number" 
                        class="form-control" 
                        id="stock" 
                        name="stock" 
                        placeholder="50"
                        min="0"
                        required
                    >
                </div>
            </div>
            
            <div class="form-group">
                <label for="status">Status *</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="image">Product Image</label>
                <input 
                    type="file" 
                    class="form-control" 
                    id="image" 
                    name="image" 
                    accept="image/*"
                    onchange="previewImage(this, 'imagePreview')"
                >
                <small style="color: #636e72;">Accepted formats: JPG, PNG, GIF (Max: 2MB)</small>
            </div>
            
            <div class="image-preview-container">
                <img id="imagePreview" src="" alt="Preview" style="display: none;">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    ✅ Create Product
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-outline">
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
handleFormSubmit('productForm');
</script>
@endpush