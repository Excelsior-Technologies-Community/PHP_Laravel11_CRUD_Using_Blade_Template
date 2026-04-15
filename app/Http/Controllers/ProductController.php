<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    /**
     * Display product list with search + pagination + summary (total count + total price)
     */

    public function index(Request $request)
    {
        $search = $request->query('search');

        $productsQuery = Product::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            });

        // Pagination
        $products = $productsQuery->paginate(5);

        // New summary variables
        $totalProducts = $productsQuery->count();       // Total products matching search
        $totalValue = $productsQuery->sum('price');    // Total price sum

        return view('products.index', compact('products', 'search', 'totalProducts', 'totalValue'));
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

    public function exportCSV(Request $request)
    {
        $search = $request->query('search');

        $products = Product::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->get();

        $filename = 'products_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ID', 'Name', 'Price', 'Description', 'Created By', 'Updated By', 'Status'];

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
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
