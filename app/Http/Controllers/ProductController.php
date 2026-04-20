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
<<<<<<< HEAD
                         ->with('success', 'Product added successfully!');
=======
            ->with('success', 'Product added successfully!');
>>>>>>> development
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
<<<<<<< HEAD
                         ->with('success', 'Product updated successfully!');
=======
            ->with('success', 'Product updated successfully!');
>>>>>>> development
    }

    public function destroy(Product $product)
    {
        $product->update(['status' => 'Deleted']);
        $product->delete();

        return redirect()->route('products.index')
<<<<<<< HEAD
                         ->with('success', 'Product deleted successfully!');
=======
            ->with('success', 'Product deleted successfully!');
>>>>>>> development
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