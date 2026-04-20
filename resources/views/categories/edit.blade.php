@extends('layouts.app')

@section('title', 'Edit Category')
@section('header', 'Edit Category')

@section('content')
<div class="card p-4" style="max-width: 600px; margin: auto;">
    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary mb-3" style="width: 150px;">
        ← Back to List
    </a>

    <form method="POST" action="{{ route('categories.update', $category->id) }}" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-12">
            <label class="form-label fw-bold">Category Name:</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $category->name) }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label fw-bold">Status:</label>
            <select name="status" class="form-select" required>
                <option value="Active" {{ $category->status == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ $category->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary px-5">Update Category</button>
        </div>
    </form>
</div>
@endsection