@extends('layouts.app')

@section('title','Product Details')
@section('header','Product Details')

@section('content')

<div class="card shadow p-4" style="max-width: 700px; margin: auto;">

    
    <!-- Title -->
    <h3 class="text-center mb-4 fw-bold">{{ $product->name }}</h3>

    <!-- Details Table -->
    <table class="table table-bordered table-striped">
        <tr>
            <th style="width: 30%;">ID</th>
            <td>{{ $product->id }}</td>
        </tr>

        <tr>
            <th>Name</th>
            <td>{{ $product->name }}</td>
        </tr>

        <tr>
            <th>Description</th>
            <td>{{ $product->description ?? '-' }}</td>
        </tr>

        <tr>
            <th>Price</th>
            <td>₹{{ number_format($product->price, 2) }}</td>
        </tr>

        <tr>
            <th>Created By</th>
            <td>{{ $product->created_by ?? '-' }}</td>
        </tr>

        <tr>
            <th>Updated By</th>
            <td>{{ $product->updated_by ?? '-' }}</td>
        </tr>

        <tr>
            <th>Status</th>
            <td>
                <span class="badge 
                    @if($product->status == 'Active') bg-success 
                    @else bg-danger 
                    @endif
                ">
                    {{ $product->status }}
                </span>
            </td>
        </tr>
    </table>

    <!-- Back Button Center -->
    <div class="text-center mt-3">
        <a href="{{ route('products.index') }}" class="btn btn-primary px-4">
            Back to List
        </a>
    </div>

</div>

@endsection
