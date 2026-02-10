@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-edit"></i> Edit Product</h1>
        <a href="{{ route('products.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="form-container">
        <div class="card">
            <form id="productForm" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name"><i class="fas fa-utensils"></i> Product Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id"><i class="fas fa-list"></i> Category *</label>
                    <select class="form-control" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ $product->description }}</textarea>
                </div>
                
                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="price"><i class="fas fa-money-bill"></i> Price (TZS) *</label>
                        <input type="number" class="form-control" id="price" name="price" value="{{ $product->price }}" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock"><i class="fas fa-boxes"></i> Stock Quantity *</label>
                        <input type="number" class="form-control" id="stock" name="stock" value="{{ $product->stock }}" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="status"><i class="fas fa-toggle-on"></i> Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="available" {{ $product->status == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ $product->status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image"><i class="fas fa-image"></i> Product Image</label>
                    @if($product->image)
                        <div style="margin-bottom: 1rem;">
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="max-width: 200px; border-radius: 8px;">
                        </div>
                    @endif
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this, 'preview')">
                    <img id="preview" style="max-width: 200px; margin-top: 1rem; border-radius: 8px; display: none;">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="button" class="btn btn-danger" onclick="deleteProduct({{ $product->id }})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
handleFormSubmit('productForm');

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

async function deleteProduct(id) {
    if (!confirm('Delete this product?')) return;
    
    try {
        const response = await fetchAPI(`/products/${id}`, { method: 'DELETE' });
        if (response.success) {
            showToast(response.message, 'success');
            setTimeout(() => window.location.href = '{{ route("products.index") }}', 1000);
        }
    } catch (error) {
        showToast(error.message || 'Failed to delete', 'error');
    }
}
</script>
@endpush