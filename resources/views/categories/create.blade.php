@extends('layouts.app')

@section('title', 'Add Category')
@section('header', 'Add Category')

@section('content')
<div class="card p-4" style="max-width: 600px; margin: auto;">
    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary mb-3" style="width: 150px;">
        ← Back to List
    </a>

    <form method="POST" action="{{ route('categories.store') }}" class="row g-3">
        @csrf

        <div class="col-12">
            <label class="form-label fw-bold">Category Name:</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                   placeholder="Enter category name" value="{{ old('name') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
            <label class="form-label fw-bold">Status:</label>
            <select name="status" class="form-select" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-success px-5">Save Category</button>
        </div>
    </form>
</div>
@endsection