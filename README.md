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


1) resources/views/layouts/app.blade.php

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>
    <!-- 
        @yield('title') → Allows child blade files to set a custom page title.
        Example: @section('title','Products List')
    -->

    <!-- Bootstrap 5 CSS CDN for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Overall page background and font */
        body {
            background-color: #f5f6fa;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Extra spacing for page content */
        .container-custom {
            margin-top: 40px;
        }

        /* Card styling (box shadow + rounded corners) */
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        /* Search bar container layout */
        .search-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Search input width */
        .search-bar input {
            width: 250px;
        }

        /* Badge color for active status */
        .badge-active {
            background-color: #28a745;
        }

        /* Badge color for deleted status */
        .badge-deleted {
            background-color: #dc3545;
        }

        /* Table header style */
        table th {
            background-color: #f8f9fa;
        }

        /* Show button styling */
        .btn-show {
            background-color: #0d6efd;
            color: #fff;
        }

        .btn-show:hover {
            background-color: #0b5ed7;
        }

        /* Edit button styling */
        .btn-edit {
            background-color: #ffc107;
            color: #fff;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        /* Delete button styling */
        .btn-delete {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        /* Center pagination block */
        .pagination {
            justify-content: center;
        }

        /* Responsive design for search bar on small screens */
        @media(max-width:768px) {
            .search-bar {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .search-bar input {
                width: 100%;
            }
        }

        /* Custom pagination styling */
        .pagination {
            display: flex;
            gap: 6px;
        }

        .page-item .page-link {
            border-radius: 8px !important;
            padding: 8px 14px;
            font-size: 14px;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease-in-out;
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
        }

        .page-item .page-link:hover {
            background-color: #e9f2ff;
            border-color: #0d6efd;
            color: #0d6efd;
        }

        .page-item.disabled .page-link {
            background-color: #f2f2f2;
            color: #b5b5b5;
            pointer-events: none;
        }

    </style>
</head>

<body>

    <div class="container container-custom">
        <!-- Page heading fetched from child view -->
        <h1 class="text-center mb-4">@yield('header')</h1>

        <!-- Dynamic page content from child blade -->
        @yield('content')
    </div>

    <!-- Bootstrap JS bundle for components (modal, dropdown, alerts etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


2) resources/views/products/index.blade.php (List Page with Pagination & Search)

@extends('layouts.app')

{{-- Page Title --}}
@section('title', 'Products List')

{{-- Page Header --}}
@section('header', 'Products List')

@section('content')
    <div class="card p-4">

        <!-- Search bar + Add product button -->
        <div class="search-bar mb-3">
            <!-- Search form (GET method so query shows in URL) -->
            <form class="d-flex" method="GET" action="{{ route('products.index') }}">
                <input class="form-control me-2" 
                       type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Search products...">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>

            <!-- Button to open create form -->
            <a href="{{ route('products.create') }}" class="btn btn-success">Add Product</a>
        </div>

        <!-- Success message -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Responsive table -->
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
                    <!-- Loop through all products -->
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>

                            <!-- Format price with 2 decimals -->
                            <td>${{ number_format($product->price, 2) }}</td>

                            <td>{{ $product->description ?? '-' }}</td>
                            <td>{{ $product->created_by ?? '-' }}</td>
                            <td>{{ $product->updated_by ?? '-' }}</td>

                            <!-- Status badge -->
                            <td>
                                <span class="badge {{ $product->status == 'Active' ? 'badge-active' : 'badge-deleted' }}">
                                    {{ $product->status }}
                                </span>
                            </td>

                            <!-- Action buttons -->
                            <td>
                                <!-- Show -->
                                <a href="{{ route('products.show', $product->id) }}" 
                                   class="btn btn-show btn-sm">Show</a>

                                <!-- Edit -->
                                <a href="{{ route('products.edit', $product->id) }}" 
                                   class="btn btn-edit btn-sm">Edit</a>

                                <!-- Delete -->
                                <form style="display:inline;" 
                                      method="POST"
                                      action="{{ route('products.destroy', $product->id) }}">
                                    @csrf 
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-delete btn-sm"
                                            onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <!-- When no products found -->
                        <tr>
                            <td colspan="8" class="text-center">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
        </div>

    </div>
@endsection


3) resources/views/products/create.blade.php (Add New Product)

@extends('layouts.app')

{{-- Page title --}}
@section('title','Add Product')

{{-- Page header --}}
@section('header','Add Product')

@section('content')

<div class="card p-4" style="max-width: 800px; margin: auto;">

    <!-- Back button -->
    <a href="{{ route('products.index') }}"
       class="btn btn-outline-secondary mb-3"
       style="width: 150px;">
        ← Back to List
    </a><br> 

    <!-- Add Product Form -->
    <form method="POST" 
          action="{{ route('products.store') }}" 
          class="row g-3 justify-content-center">

        @csrf <!-- CSRF Protection -->

        <!-- Product Name Field -->
        <div class="col-12">
            <label class="form-label fw-semibold">Product Name:</label>
            <input type="text"
                   name="name"
                   class="form-control mx-auto"
                   placeholder="Enter product name"
                   required
                   style="max-width: 400px;">
        </div>

        <!-- Price Field -->
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

        <!-- Description Field -->
        <div class="col-12">
            <label class="form-label fw-semibold">Description:</label>
            <textarea name="description"
                      class="form-control mx-auto"
                      rows="4"
                      placeholder="Enter description"
                      style="max-width: 400px;"></textarea>
        </div>

        <!-- Submit Button -->
        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-success px-4 py-2">
                Add Product
            </button>
        </div>

    </form>
</div>

@endsection


4) resources/views/products/edit.blade.php (Edit Product)

@extends('layouts.app')

{{-- Page title --}}
@section('title','Edit Product')

{{-- Page header --}}
@section('header','Edit Product')

@section('content')

<div class="card p-4" style="max-width: 800px; margin: auto;">

    <!-- Back button -->
    <a href="{{ route('products.index') }}"
       class="btn btn-outline-secondary mb-3"
       style="width: 150px;">
        ← Back to List
    </a>

    <!-- Edit Form -->
    <form method="POST"
          action="{{ route('products.update', $product->id) }}"
          class="row g-3 justify-content-center">

        @csrf
        @method('PUT') <!-- PUT method for update request -->

        <!-- Product Name Field -->
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

        <!-- Price Field -->
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

        <!-- Description Field -->
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



5) resources/views/products/show.blade.php (Show Product)


@extends('layouts.app')

{{-- Page title --}}
@section('title','Product Details')

{{-- Page header --}}
@section('header','Product Details')

@section('content')

<div class="card shadow p-4" style="max-width: 700px; margin: auto;">

    <!-- Product Title -->
    <h3 class="text-center mb-4 fw-bold">{{ $product->name }}</h3>

    <!-- Product Details Table -->
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
                <!-- Status badge -->
                <span class="badge 
                    @if($product->status == 'Active') 
                        bg-success 
                    @else 
                        bg-danger 
                    @endif">
                    {{ $product->status }}
                </span>
            </td>
        </tr>

    </table>

    <!-- Back Button -->
    <div class="text-center mt-3">
        <a href="{{ route('products.index') }}" class="btn btn-primary px-4">
            Back to List
        </a>
    </div>

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
