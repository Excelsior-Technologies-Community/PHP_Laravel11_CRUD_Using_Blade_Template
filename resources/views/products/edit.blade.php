@extends('layouts.app')

@section('title','Edit Product')
@section('header','Edit Product')

@section('content')

<div class="card p-4" style="max-width: 800px; margin: auto;">

    <!-- Small Back Button -->
    <a href="{{ route('products.index') }}"
       class="btn btn-outline-secondary mb-3"
       style="width: 150px;">
        ← Back to List
    </a>

    <!-- Form -->
    <form method="POST" action="{{ route('products.update', $product->id) }}" class="row g-3 justify-content-center">
        @csrf
        @method('PUT')

        <!-- Product Name -->
        <div class="col-12">
            <label class="form-label fw-semibold">Product Name:</label>
            <input type="text"
                   name="name"
                   value="{{ $product->name }}"
                   class="form-control mx-auto"
                   placeholder="Enter product name"
                   required
                   style="max-width: 400px;">
        </div>

        <!-- Price -->
        <div class="col-12">
            <label class="form-label fw-semibold">Price:</label>
            <input type="number"
                   step="0.01"
                   name="price"
                   value="{{ $product->price }}"
                   class="form-control mx-auto"
                   placeholder="Enter price"
                   required
                   style="max-width: 400px;">
        </div>

        <!-- Description -->
        <div class="col-12">
            <label class="form-label fw-semibold">Description:</label>
            <textarea name="description"
                      class="form-control mx-auto"
                      rows="4"
                      placeholder="Enter description"
                      style="max-width: 400px;">{{ $product->description }}</textarea>
        </div>

        <!-- Update Button -->
        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-success px-4 py-2">
                Update Product
            </button>
        </div>
    </form>
</div>

@endsection
