@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Categories
    </a>
</div>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Category</h5>
    </div>
    <div class="card-body">
        <form id="categoryForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="category_id" value="{{ $category->id }}">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Display Order</label>
                    <input type="number" class="form-control" name="display_order" value="{{ $category->display_order }}" min="0">
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3">{{ $category->description }}</textarea>
                <div class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Category Image</label>
                @if($category->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                @endif
                <input type="file" class="form-control" name="image" accept="image/*" id="categoryImage">
                <small class="text-muted">Leave empty to keep current image</small>
                <div class="invalid-feedback"></div>
                <div class="mt-2" id="imagePreview"></div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save"></i> Update Category
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/categories.js') }}"></script>
<script>
    // Image preview
    document.getElementById('categoryImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').innerHTML = 
                    '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush