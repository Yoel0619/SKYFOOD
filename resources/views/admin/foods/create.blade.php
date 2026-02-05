@extends('layouts.admin')

@section('title', 'Add New Food')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.foods.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Foods
    </a>
</div>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-plus"></i> Add New Food</h5>
    </div>
    <div class="card-body">
        <form id="foodForm" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Food Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
                <div class="invalid-feedback"></div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Price (TSh) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Discount (%)</label>
                    <input type="number" class="form-control" name="discount_percentage" step="0.01" min="0" max="100" value="0">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Preparation Time (mins)</label>
                    <input type="number" class="form-control" name="preparation_time" min="0" value="20">
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Calories</label>
                    <input type="number" class="form-control" name="calories" min="0">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Food Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*" id="foodImage">
                    <small class="text-muted">Max size: 2MB</small>
                    <div class="invalid-feedback"></div>
                    <div class="mt-2" id="imagePreview"></div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Ingredients</label>
                <textarea class="form-control" name="ingredients" rows="2" placeholder="List main ingredients"></textarea>
                <div class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Allergen Information</label>
                <textarea class="form-control" name="allergen_info" rows="2" placeholder="e.g., Contains nuts, dairy"></textarea>
                <div class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="is_vegetarian" id="is_vegetarian" value="1">
                    <label class="form-check-label" for="is_vegetarian">
                        Vegetarian
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1" checked>
                    <label class="form-check-label" for="is_available">
                        Available
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save"></i> Save Food
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/foods.js') }}"></script>
<script>
    // Image preview
    document.getElementById('foodImage').addEventListener('change', function(e) {
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