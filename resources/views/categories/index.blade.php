@extends('layouts.app')

@section('title', 'Categories List')
@section('header', 'Categories List')

@section('content')
<div class="card p-4">
    <div class="search-bar mb-3 d-flex flex-wrap align-items-center gap-2">
        <form class="d-flex flex-grow-1" method="GET" action="{{ route('categories.index') }}">
            <input class="form-control me-2" type="text" name="search" value="{{ $search }}"
                placeholder="Search categories...">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>

        <a href="{{ route('categories.create') }}" class="btn btn-success">
            + Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td class="fw-bold">{{ $category->name }}</td>
                    <td>
                        <span class="badge {{ $category->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                            {{ $category->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        
                        <form style="display:inline;" method="POST" action="{{ route('categories.destroy', $category->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">No categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $categories->appends(['search' => $search])->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>
@endsection