# PHP_Laravel11_CRUD_Using_Blade_Template

**Project Name:** PHP_Laravel11_CRUD_Using_Blade_Template 
**Author:** Manasi Patel  
**Date:** 2025  
**Laravel Version:** 11  

A simple Product Management System built with Laravel 11, demonstrating CRUD operations with search and pagination.

---

##  Overview

This project teaches you how to:

- Create, read, update, and delete products (CRUD).
- Implement search functionality to find products by name, price, ID, or description.
- Paginate the product list for a cleaner UI.
- Use Blade templates and Bootstrap 5 for frontend design.

All code includes comments explaining each step, making it beginner-friendly.

---

##  Features

-  Add, edit, delete, and view products  
-  Search products by name, price, ID, or description  
-  Pagination (5 products per page)  
-  Soft delete with restore functionality  
-  Clean UI with Bootstrap 5  

---

##  1. Project Setup

# Install Laravel 11
```
composer create-project laravel/laravel PHP_Laravel11_CRUD_Using_Blade_Template "^11.0"
```
# Navigate to project
```
cd PHP_Laravel11_CRUD_Using_Blade_Template
```
⚙ 2. Configure Database
Create a database named crud_app:

sql

CREATE DATABASE crud_app;
Update .env:

makefile
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_app
DB_USERNAME=root
DB_PASSWORD=
```

🗄 3. Migration (Products Table)
Create migration:


```
php artisan make:migration create_products_table --create=products
```
Edit migration database/migrations/YYYY_MM_DD_create_products_table.php:

```

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```
Run migration:

```
php artisan migrate
```
 4. Model and Controller
Generate model & controller:

```

php artisan make:controller ProductController --resource --model=Product
php artisan make:model Product

```
Product Model - app/Models/Product.php:
```

 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'price', 'created_by', 'updated_by', 'status'
    ];
}
```
Product Controller - app/Http/Controllers/ProductController.php:

```

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $products = Product::query()
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('price', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->paginate(5);

        return view('products.index', compact('products', 'search'));
    }

    public function create() { return view('products.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
        ]);

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 'Active',
        ]);

        return redirect()->route('products.index')
                         ->with('success', 'Product added successfully!');
    }

    public function edit(Product $product)
{ return view('products.edit', compact('product')); }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'updated_by' => 1,
        ]);

        return redirect()->route('products.index')
                         ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->update(['status' => 'Deleted']);
        $product->delete();

        return redirect()->route('products.index')
                         ->with('success', 'Product deleted successfully!');
    }

    public function show(Product $product) { return view('products.show', compact('product')); }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        $product->update(['status' => 'Active']);

        return redirect()->route('products.index')
                         ->with('success', 'Product restored successfully!');
    }
}
```
 5. Routes
routes/web.php:

```
<?php
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('products.index'));

Route::resource('products', ProductController::class);

Route::get('products/restore/{id}', [ProductController::class, 'restore'])
    ->name('products.restore');

```
 6. Blade Views
6.1 Layout - resources/views/layouts/app.blade.php
```

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #f5f6fa; font-family: 'Segoe UI', sans-serif; }
        .container-custom { margin-top: 40px; }
        .card { border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .search-bar { display:flex; justify-content:space-between; margin-bottom:20px; }
        .search-bar input { width:250px; }
        .badge-active { background-color:#28a745; }
        .badge-deleted { background-color:#dc3545; }
        .pagination { justify-content:center; display:flex; gap:6px; }
        .page-item .page-link { border-radius:8px; padding:8px 14px; font-size:14px; }
        .page-item.active .page-link { background-color:#0d6efd; color:white; border-color:#0d6efd; }
        @media(max-width:768px) { .search-bar { flex-direction:column; gap:10px; } .search-bar input{width:100%;} }
    </style>
</head>
<body>
<div class="container container-custom">
    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```
6.2 Product Index - resources/views/products/index.blade.php
```

@extends('layouts.app')

@section('title', 'Products List')

@section('content')
<div class="card p-4">
    <div class="search-bar">
        <h3>Products</h3>
        <form action="{{ route('products.index') }}" method="GET">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $search }}">
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Add New Product</a>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="20%">Name</th>
                <th>Description</th>
                <th width="15%">Price</th>
                <th width="10%">Status</th>
                <th width="25%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ Str::limit($product->description, 40) }}</td>
                    <td>₹ {{ number_format($product->price, 2) }}</td>
                    <td>
                        @if($product->status == 'Active')
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-deleted">Deleted</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-show btn-sm">View</a>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-edit btn-sm">Edit</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-delete btn-sm" onclick="return confirm('Delete this product?')">Delete</button>
                        </form>
                        @if($product->deleted_at)
                            <a href="{{ route('products.restore', $product->id) }}" class="btn btn-info btn-sm">Restore</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $products->appends(['search'=>$search])->links() }}
</div>
@endsection
```
6.3 Product Create - resources/views/products/create.blade.php
```

@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="card p-4">
    <h3>Add New Product</h3>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Product Name *</label>
            <input type="text" name="name" class="form-control" required>
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Price *</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
            @error('price') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button class="btn btn-primary">Save Product</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
```
6.4 Product Edit - resources/views/products/edit.blade.php
```
@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="card p-4">
    <h3>Edit Product</h3>

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Product Name *</label>
            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control">{{ $product->description }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Price *</label>
            <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control" required>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
```
6.5 Product Show - resources/views/products/show.blade.php
```

@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="card p-4">
    <h3>Product Details</h3>

    <p><strong>ID:</strong> {{ $product->id }}</p>
    <p><strong>Name:</strong> {{ $product->name }}</p>
    <p><strong>Description:</strong> {{ $product->description }}</p>
    <p><strong>Price:</strong> ₹ {{ number_format($product->price, 2) }}</p>
    <p><strong>Status:</strong> {{ $product->status }}</p>

    <a href="{{ route('products.index') }}" class="btn btn-primary">Back</a>
</div>
@endsection
```
 7. Run the Application
```

php artisan serve
```
Open in browser:
```
http://localhost:8000/products
```

You can show this type Output : 
index page:
<img width="1911" height="969" alt="Screenshot 2025-12-03 132129" src="https://github.com/user-attachments/assets/6f7a0db3-13ae-4831-8cf9-71aaf9b7e673" />
add product page :
<img width="1919" height="964" alt="Screenshot 2025-12-03 132247" src="https://github.com/user-attachments/assets/a110e8ec-4ed9-4b55-9200-38697fdad70b" />
edit product page :
<img width="1911" height="962" alt="image" src="https://github.com/user-attachments/assets/f4c70bbd-fd07-4116-8b72-05c5dd371250" />
show product page :
<img width="1919" height="972" alt="Screenshot 2025-12-03 132300" src="https://github.com/user-attachments/assets/36723cfe-1f96-4dc3-abb4-70c4aadc5fc7" />


You can now:

Add, edit, and delete products

Search products

View product details

Restore soft-deleted products

 8. Project Structure
```

PHP_Laravel11_CRUD_Using_Blade_Template
│
├── app
│   ├── Models/Product.php
│   └── Http/Controllers/ProductController.php
│
├── database/migrations/XXXX_create_products_table.php
├── resources/views/layouts/app.blade.php
├── resources/views/products/index.blade.php
├── resources/views/products/create.blade.php
├── resources/views/products/edit.blade.php
├── resources/views/products/show.blade.php
├── routes/web.php
├── .env
└── composer.json
```
 Completed!
