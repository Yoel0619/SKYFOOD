@extends('layouts.admin')

@section('title', 'Manage Foods')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-utensils"></i> Foods</h1>
    <a href="{{ route('admin.foods.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Food
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.foods.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Search foods..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Foods Table -->
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($foods as $food)
                        <tr>
                            <td>{{ $food->id }}</td>
                            <td>
                                @if($food->image)
                                    <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-white"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $food->name }}</strong>
                                @if($food->is_vegetarian)
                                    <span class="badge bg-success">Veg</span>
                                @endif
                            </td>
                            <td>{{ $food->category->name }}</td>
                            <td>
                                @if($food->discount_percentage > 0)
                                    <span class="text-decoration-line-through text-muted">TSh {{ number_format($food->price, 0) }}</span>
                                    <br>
                                    <span class="text-primary fw-bold">TSh {{ number_format($food->final_price, 0) }}</span>
                                @else
                                    <span class="fw-bold">TSh {{ number_format($food->price, 0) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($food->discount_percentage > 0)
                                    <span class="badge bg-danger">{{ $food->discount_percentage }}%</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm {{ $food->is_available ? 'btn-success' : 'btn-secondary' }} toggle-availability" data-id="{{ $food->id }}">
                                    {{ $food->is_available ? 'Available' : 'Unavailable' }}
                                </button>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.foods.edit', $food->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-food" data-id="{{ $food->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No foods found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $foods->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/foods.js') }}"></script>
@endpush