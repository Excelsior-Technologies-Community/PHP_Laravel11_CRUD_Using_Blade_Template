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
        ->when($search, function($query, $search){
            return $query->where('name', 'like', "%{$search}%");
        })
        ->paginate(5);

    return view('products.index', compact('products', 'search'));
}

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'price'=>'required|numeric',
        ]);

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'created_by' => 1,
            'updated_by' => 1,
             'status' => 'Active', // store Active
        ]);

        return redirect()->route('products.index')->with('success','Product added successfully!');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'=>'required',
            'price'=>'required|numeric',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'updated_by' => 1,
        ]);

        return redirect()->route('products.index')->with('success','Product updated successfully!');
    }

 public function destroy(Product $product)
{
    $product->update(['status' => 'Deleted']); // mark as Deleted
    $product->delete(); // soft delete
    return redirect()->route('products.index')->with('success','Product deleted successfully!');
}


    // public function restore($id)
    // {
    //     $product = Product::withTrashed()->findOrFail($id);
    //     $product->restore();
    //     return redirect()->route('products.index')->with('success','Product restored successfully!');
    // }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}
