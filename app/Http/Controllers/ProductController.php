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
