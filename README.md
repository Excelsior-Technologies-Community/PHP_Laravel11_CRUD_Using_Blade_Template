Laravel 11 CRUD Application Tutorial for Beginners (With Pagination & Search)

By: Manasi Patel / CRUDSearchApp
Date: 2025
Laravel Version: 11

In this tutorial, we are going to build a simple Product Management System using Laravel 11, where you can create, read, update, and delete products. CRUD operations are the backbone of any web application, and learning how to implement them will help you become a proficient Laravel developer.

We will also implement:

Search functionality – to find products by their name easily

Pagination – to display products in pages instead of one long list

This tutorial is beginner-friendly and fully explained step by step, so even if you are new to Laravel, you can follow along. By the end of this tutorial, you will be able to create your own CRUD applications with clean and professional UI.

We will cover:

Installing Laravel 11 and setting up the environment

Configuring the database

Creating migration and products table

Building Eloquent models

Creating resource controllers with CRUD methods

Defining routes

Designing the frontend with Blade templates and Bootstrap 5

Implementing search and pagination

All code will include comments explaining what each part does, so you can understand the purpose and function of every line.




Step 1: Install Laravel 11

First, install a fresh Laravel 11 project using Composer:

composer create-project laravel/laravel CRUDSearchApp "^11.0"


Navigate to your project folder:

cd CRUDSearchApp


Now your Laravel 11 project is ready to configure.


Step 2: Configure Database

Open the .env file and update the database credentials:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_app
DB_USERNAME=root
DB_PASSWORD=


Next, create a database named crud_app in phpMyAdmin or MySQL CLI:

CREATE DATABASE crud_app;



Step 3: Create Migration for Products Table

To store products, we need a table. Run:

php artisan make:migration create_products_table --create=products


Open the generated migration file in database/migrations/xxxx_xx_xx_create_products_table.php and add:

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This function executes when we run: php artisan migrate
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            // Primary Key (Auto Increment)
            $table->id();  
            // Creates 'id' column (BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY)

            // Product Name
            $table->string('name');
            // Stores product name using VARCHAR(255)

            // Product Description
            $table->text('description')->nullable();
            // 'text' allows long description, and nullable() makes it optional

            // Product Price
            $table->decimal('price', 10, 2);
            // decimal(10,2) means: max 10 digits total, 2 digits after the decimal

            // User who created the product
            $table->unsignedBigInteger('created_by')->nullable();
            // Stores user ID (foreign key in future) and can be null

            // User who last updated the product
            $table->unsignedBigInteger('updated_by')->nullable();
            // Same as above — stores user ID and can be null

            // Product Status
            $table->string('status')->default('Active');
            // Default value = 'Active' if no status provided

            // Timestamp Columns
            $table->timestamps();
            // Creates 'created_at' & 'updated_at' columns automatically

            // Soft Delete Column
            $table->softDeletes();
            // Creates 'deleted_at' column → used for soft delete
        });
    }

    /**
     * Reverse the migrations.
     * Runs when we use: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        // Drops the 'products' table only if it already exists
    }
};


Run the migration:

php artisan migrate


Your products table is now created.



Step 4: Add Routes

Open routes/web.php and add the following resource route for CRUD operations:

<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

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



Step 5: Create Model and Controller

Run this command to generate a Product model and a resource controller with CRUD methods:

php artisan make:controller ProductController --resource --model=Product


This command creates:

app/Models/Product.php – The Eloquent model

app/Http/Controllers/ProductController.php – The controller with index, create, store, show, edit, update, destroy methods



Product Model

Open app/Models/Product.php:

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
        'name',          // Product name
        'description',   // Product details
        'price',         // Product price
        'created_by',    // User ID who created the product
        'updated_by',    // User ID who last updated the product
        'status',        // Product status (Active/Inactive)
    ];
}




ProductController

Open app/Http/Controllers/ProductController.php and replace with:

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display product list with search + pagination
     */
    public function index(Request $request)
    {
        // Get search input from URL query (?search=value)
        $search = $request->query('search');

        // Query products with optional search
        $products = Product::query()
            ->when($search, function($query, $search) {
                // If search exists, filter by multiple columns
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('price', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->paginate(5); // Paginate results, 5 products per page

        // Pass products and search value to view
        return view('products.index', compact('products', 'search'));
    }


    /**
     * Show product create form
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store new product in database
     */
    public function store(Request $request)
    {
        // Form validation rules
        $request->validate([
            'name'  => 'required',
            'price' => 'required|numeric',
        ]);

        // Insert new product record
        Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'created_by'  => 1, // Hardcoded for now (later you can use Auth)
            'updated_by'  => 1,
            'status'      => 'Active', // Default status
        ]);

        // Redirect back with success message
        return redirect()->route('products.index')
                         ->with('success', 'Product added successfully!');
    }

    /**
     * Show the edit form
     */
    public function edit(Product $product)
    {
        // Pass selected product to edit page
        return view('products.edit', compact('product'));
    }

    /**
     * Update product details
     */
    public function update(Request $request, Product $product)
    {
        // Validate form
        $request->validate([
            'name'  => 'required',
            'price' => 'required|numeric',
        ]);

        // Update product fields
        $product->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'updated_by'  => 1, // Hardcoded for now
        ]);

        return redirect()->route('products.index')
                         ->with('success', 'Product updated successfully!');
    }

    /**
     * Soft delete a product (mark deleted + soft delete)
     */
    public function destroy(Product $product)
    {
        // update status to Deleted
        $product->update(['status' => 'Deleted']);

        // Soft delete (sets deleted_at)
        $product->delete();

        return redirect()->route('products.index')
                         ->with('success', 'Product deleted successfully!');
    }

    /**
     * Show product details page
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}



Step 6: Create Blade Views

Create a folder resources/views/layouts/ and create these files:
app.blade.php

Create a folder resources/views/products/ and create these files:

index.blade.php,
create.blade.php,
edit.blade.php, 
show.blade.php


1. resources/views/layouts/app.blade.php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f6fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .container-custom {
            margin-top: 40px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .search-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 250px;
        }

        .badge-active { background-color: #28a745; }
        .badge-deleted { background-color: #dc3545; }

        table th { background-color: #f8f9fa; }

        .btn-show { background-color: #0d6efd; color: white; }
        .btn-show:hover { background-color: #0b5ed7; }

        .btn-edit { background-color: #ffc107; color: white; }
        .btn-edit:hover { background-color: #e0a800; }

        .btn-delete { background-color: #dc3545; color: white; }
        .btn-delete:hover { background-color: #c82333; }

        .pagination {
            justify-content: center;
            display: flex;
            gap: 6px;
        }

        .page-item .page-link {
            border-radius: 8px !important;
            padding: 8px 14px;
            font-size: 14px;
            border: 1px solid #dee2e6;
            transition: 0.2s ease-in-out;
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        @media (max-width: 768px) {
            .search-bar {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            .search-bar input { width: 100%; }
        }
    </style>
</head>

<body>

<div class="container container-custom">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

✅ 2. resources/views/products/index.blade.php
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

                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                            style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-delete btn-sm" onclick="return confirm('Delete this product?')">
                                Delete
                            </button>
                        </form>

                        @if($product->deleted_at)
                            <a href="{{ route('products.restore', $product->id) }}" class="btn btn-info btn-sm">
                                Restore
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No products found.</td></tr>
            @endforelse
        </tbody>

    </table>

    {{ $products->appends(['search' => $search])->links() }}

</div>

@endsection

✅ 3. resources/views/products/create.blade.php
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

✅ 4. resources/views/products/edit.blade.php
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

✅ 5. resources/views/products/show.blade.php
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

Step 7: Run the Application
php artisan serve

Open browser:

http://localhost:8000/products



Now you can:

Add products

Edit products

Delete products

Search products by name

Paginate the product list



✅ Congratulations! You now have a fully functional Laravel 11 CRUD application with search and pagination
