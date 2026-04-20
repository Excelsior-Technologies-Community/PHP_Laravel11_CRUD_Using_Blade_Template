# PHP_Laravel11_CRUD_Using_Blade_Template


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
    /**
     * HasFactory  → Allows use of model factories (helpful for testing & seeding)
     * SoftDeletes → Enables soft delete feature (uses deleted_at column)
     */

    /**
     * The attributes that are mass assignable.
     * fillable protects the model from mass assignment vulnerabilities.
     * Only these fields can be filled using create() or update().
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'created_by',
        'updated_by',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```
Product Controller - app/Http/Controllers/ProductController.php:

```

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $productsQuery = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', function($catQuery) use ($search) {
                            $catQuery->where('name', 'like', "%{$search}%");
                        });
                });
            });

        $products = $productsQuery->paginate(5);

        $totalProducts = $productsQuery->count();
        $totalValue = $productsQuery->sum('price');

        return view('products.index', compact('products', 'search', 'totalProducts', 'totalValue'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'  => 'required',
            'price' => 'required|numeric',
        ]);

        Product::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'created_by'  => 1,
            'updated_by'  => 1,
            'status'      => 'Active',
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product added successfully!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'  => 'required',
            'price' => 'required|numeric',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'updated_by'  => 1,
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

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function exportCSV(Request $request)
    {
        $search = $request->query('search');

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', function($catQuery) use ($search) {
                            $catQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->get();

        $filename = 'products_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ID', 'Name', 'Category', 'Price', 'Description', 'Created By', 'Updated By', 'Status'];

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->category->name ?? '-',
                    $product->price,
                    $product->description,
                    $product->created_by,
                    $product->updated_by,
                    $product->status,
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

```
 5. Routes
routes/web.php:

```
<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

/**
 * Redirect root URL (/) to products list page.
 * When user opens the home page, we directly send them to products.index route.
 */
Route::get('/', function () {
    return redirect()->route('products.index');
});

/**
 * Resource Route for CRUD
 * This single line creates all 7 routes:
 * index, create, store, show, edit, update, destroy
 */
Route::resource('products', ProductController::class);

/**
 * Restore Deleted Product (Soft Delete Restore)
 * This route is used when you want to restore a soft-deleted product.
 * Example URL: /products/restore/5
 */
Route::get('products/restore/{id}', [ProductController::class, 'restore'])
    ->name('products.restore');

// Export products as CSV
Route::get('products/export/csv', [ProductController::class, 'exportCSV'])->name('products.export.csv');

// Category Routes
Route::resource('categories', CategoryController::class);


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

@extends('layouts.app') @section('title', 'Products List') @section('header', 'Products List') @section('content')
<div class="card p-4"> <div class="search-bar mb-3 d-flex flex-wrap align-items-center gap-2">
        <form class="d-flex flex-grow-1" method="GET" action="{{ route('products.index') }}">
            <input class="form-control me-2" type="text" name="search" value="{{ $search }}"
                placeholder="Search products...">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>

        <a href="{{ route('categories.index') }}" class="btn btn-info">
            Categories
        </a>

        <a href="{{ route('products.create') }}" class="btn btn-success">
            Add Product
        </a>

        <a href="{{ route('products.export.csv', ['search' => $search]) }}" class="btn btn-warning">
            Export CSV
        </a>
    </div>

    <div class="mb-3">
        <strong>Total Products:</strong> {{ $totalProducts }} |
        <strong>Total Value:</strong> ₹{{ number_format($totalValue, 2) }}
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th> <th>Price</th>
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
                    <td>
                        <span class="badge bg-secondary">
                            {{ $product->category->name ?? '-' }}
                        </span>
                    </td> <td>₹{{ number_format($product->price, 2) }}</td>
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
                    <td colspan="9" class="text-center">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
    </div>

</div>
@endsection
```
6.3 Product Create - resources/views/products/create.blade.php
```

@extends('layouts.app')

@section('title','Add Product')
@section('header','Add Product')

@section('content')

<div class="card p-4" style="max-width: 800px; margin: auto;">

    <a href="{{ route('products.index') }}"
       class="btn btn-outline-secondary mb-3"
       style="width: 150px;">
        ← Back to List
    </a><br> 

    <form method="POST" action="{{ route('products.store') }}" class="row g-3 justify-content-center">
        @csrf

        <div class="col-12">
            <label class="form-label fw-semibold">Category:</label>
            <select name="category_id" class="form-control mx-auto" style="max-width: 400px;" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Product Name:</label>
            <input type="text"
                   name="name"
                   class="form-control mx-auto"
                   placeholder="Enter product name"
                   required
                   style="max-width: 400px;">
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Price:</label>
            <input type="number"
                   step="0.01"
                   name="price"
                   class="form-control mx-auto"
                   placeholder="Enter price"
                   required
                   style="max-width: 400px;">
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Description:</label>
            <textarea name="description"
                      class="form-control mx-auto"
                      rows="4"
                      placeholder="Enter description"
                      style="max-width: 400px;"></textarea>
        </div>

        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-success px-4 py-2">
                Add Product
            </button>
        </div>
    </form>
</div>

@endsection
```
6.4 Product Edit - resources/views/products/edit.blade.php
```
@extends('layouts.app')

@section('title','Edit Product')
@section('header','Edit Product')

@section('content')

<div class="card p-4" style="max-width: 800px; margin: auto;">

    <a href="{{ route('products.index') }}"
       class="btn btn-outline-secondary mb-3"
       style="width: 150px;">
        ← Back to List
    </a>

    <form method="POST" action="{{ route('products.update', $product->id) }}" class="row g-3 justify-content-center">
        @csrf
        @method('PUT')

        <div class="col-12">
            <label class="form-label fw-semibold">Category:</label>
            <select name="category_id" class="form-control mx-auto" style="max-width: 400px;" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

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

        <div class="col-12">
            <label class="form-label fw-semibold">Description:</label>
            <textarea name="description"
                      class="form-control mx-auto"
                      rows="4"
                      placeholder="Enter description"
                      style="max-width: 400px;">{{ $product->description }}</textarea>
        </div>

        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-success px-4 py-2">
                Update Product
            </button>
        </div>
    </form>
</div>

@endsection
```
6.5 Product Show - resources/views/products/show.blade.php
```

@extends('layouts.app')

@section('title','Product Details')
@section('header','Product Details')

@section('content')

<div class="card shadow p-4" style="max-width: 700px; margin: auto;">

    
    <h3 class="text-center mb-4 fw-bold">{{ $product->name }}</h3>

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
            <th>Category</th>
            <td>{{ $product->category->name ?? '-' }}</td>
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

    <div class="text-center mt-3">
        <a href="{{ route('products.index') }}" class="btn btn-primary px-4">
            Back to List
        </a>
    </div>

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

index product page:
<img width="1917" height="891" alt="Screenshot 2026-04-17 183359" src="https://github.com/user-attachments/assets/4344ffd8-58a6-4865-861a-5c0ed09cef82" />

add product page :
<img width="1915" height="906" alt="Screenshot 2026-04-17 183117" src="https://github.com/user-attachments/assets/1fb63a40-f4f7-4db6-91ff-977cebf1bebf" />

edit product page :
<img width="1919" height="910" alt="Screenshot 2026-04-17 183136" src="https://github.com/user-attachments/assets/4585f206-9a62-4519-8c48-21d50d1eee7b" />

show product page :
<img width="1919" height="913" alt="Screenshot 2026-04-17 183125" src="https://github.com/user-attachments/assets/c2fc5893-15f2-4a23-8430-f230338e77af" />


index category page:
<img width="1918" height="650" alt="Screenshot 2026-04-17 182117" src="https://github.com/user-attachments/assets/5dd5d324-79dc-457f-baf5-5ea807617abe" />

add category page :
<img width="1919" height="894" alt="Screenshot 2026-04-17 182129" src="https://github.com/user-attachments/assets/bdd0fa63-27cb-4ad0-a74f-1d0e9828f3a1" />

edit category page :
<img width="1915" height="904" alt="Screenshot 2026-04-17 182142" src="https://github.com/user-attachments/assets/60cc0207-3710-4e2a-a268-d82c14b17ebd" />

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
