@extends('layouts.app') <!-- Extends the main layout file -->

@section('title', 'Products List') <!-- Sets the page title -->

@section('header', 'Products List') <!-- Sets the page header -->

@section('content')
<div class="card p-4"> <!-- Card container with padding -->

    <!-- Search bar, Add Product, and Export CSV button -->
    <div class="search-bar mb-3 d-flex flex-wrap align-items-center gap-2">
        <!-- Search form -->
        <form class="d-flex flex-grow-1" method="GET" action="{{ route('products.index') }}">
            <input class="form-control me-2" type="text" name="search" value="{{ $search }}"
                placeholder="Search products...">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>

        <!-- Add Product button -->
        <a href="{{ route('products.create') }}" class="btn btn-success">
            Add Product
        </a>

        <!-- Export CSV button -->
        <a href="{{ route('products.export.csv', ['search' => $search]) }}" class="btn btn-warning">
            Export CSV
        </a>
    </div>

    <!-- Product Summary -->
    <div class="mb-3">
        <strong>Total Products:</strong> {{ $totalProducts }} |
        <strong>Total Value:</strong> ₹{{ number_format($totalValue, 2) }}
    </div>

    <!-- Success message after actions like create, update, delete -->
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Table responsive wrapper -->
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Created By</th>
                    <th>Updated By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>₹{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->description ?? '-' }}</td>
                    <td>{{ $product->created_by ?? '-' }}</td>
                    <td>{{ $product->updated_by ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $product->status == 'Active' ? 'badge-active' : 'badge-deleted' }}">
                            {{ $product->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-show btn-sm">Show</a>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-edit btn-sm">Edit</a>
                        <form style="display:inline;" method="POST"
                            action="{{ route('products.destroy', $product->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-delete btn-sm"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination links centered -->
    <div class="d-flex justify-content-center mt-4">
        {{ $products->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
    </div>

</div>
@endsection