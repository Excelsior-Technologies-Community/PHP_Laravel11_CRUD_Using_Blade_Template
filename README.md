# Laravel 11 CRUD Application (With Pagination & Search)

**By:** Manasi Patel
**Project Name:** CRUDSearchApp
**Laravel Version:** 11
**Date:** 2025

This is a **simple Product Management System** built using Laravel 11. It allows users to **create, read, update, and delete products** with search and pagination functionalities. This tutorial-style project is beginner-friendly and fully explained.

---

## Features

* Add, edit, and delete products
* Search products by name, ID, price, or description
* Pagination to display products in pages
* Soft delete with restore option
* Clean and professional UI using Bootstrap 5

---

## Installation & Setup

### Step 1: Install Laravel 11

```bash
composer create-project laravel/laravel CRUDSearchApp "^11.0"
cd CRUDSearchApp
```

---

### Step 2: Configure Database

Update `.env` file with your database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_app
DB_USERNAME=root
DB_PASSWORD=
```

Create the database in MySQL:

```sql
CREATE DATABASE crud_app;
```

---

### Step 3: Create Products Table Migration

Generate migration:

```bash
php artisan make:migration create_products_table --create=products
```

Edit the migration file to define columns (`id`, `name`, `description`, `price`, `created_by`, `updated_by`, `status`, timestamps, soft delete).

Run migration:

```bash
php artisan migrate
```

---

### Step 4: Add Routes

Open `routes/web.php` and add:

```php
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::resource('products', ProductController::class);
Route::get('products/restore/{id}', [ProductController::class, 'restore'])->name('products.restore');
```

---

### Step 5: Create Model & Controller

Generate model and controller:

```bash
php artisan make:controller ProductController --resource --model=Product
```

* **Product Model:**

  * Uses `HasFactory` and `SoftDeletes`
  * Fillable fields: `name`, `description`, `price`, `created_by`, `updated_by`, `status`

* **ProductController:**

  * `index()` → list products with search & pagination
  * `create()` → show form
  * `store()` → add new product
  * `edit()` → show edit form
  * `update()` → update product
  * `destroy()` → soft delete product
  * `show()` → view product details
  * `restore()` → restore soft-deleted product

---

### Step 6: Blade Views

Create the following **Blade views** (in `resources/views`):

* `layouts/app.blade.php` → main layout
* `products/index.blade.php` → product list
* `products/create.blade.php` → add product form
* `products/edit.blade.php` → edit form
* `products/show.blade.php` → product details

> **Note:** Blade files include Bootstrap 5 styling, search bar, pagination, status badges, and action buttons.

---

### Step 7: Run the Application

```bash
php artisan serve
```

Open in browser:

```
http://localhost:8000/products
```

---

## Usage

* Add new products
* Edit existing products
* Delete products (soft delete)
* Search products by name, ID, price, or description
* Paginate product list
* Restore deleted products

---

## Commands Summary

```bash
# Create Laravel project
composer create-project laravel/laravel CRUDSearchApp "^11.0"

# Navigate to project
cd CRUDSearchApp

# Create migration for products table
php artisan make:migration create_products_table --create=products

# Run migration
php artisan migrate

# Generate controller with model
php artisan make:controller ProductController --resource --model=Product

# Serve the application
php artisan serve
```

---

## Technologies Used

* **Laravel 11**
* **PHP 8+**
* **MySQL**
* **Bootstrap 5**
* **Blade Templates**

---

✅ **Congratulations!** Your Laravel 11 CRUD application is fully functional with **search, pagination, and soft delete** features.
