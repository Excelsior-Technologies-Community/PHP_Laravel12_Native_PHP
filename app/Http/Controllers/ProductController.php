<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->oldest()
            ->paginate(3)
            ->withQueryString();


        $categories = Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');


        return view('products.index', compact('products', 'categories'));
    }


    /**
     * Show create form.
     */
    public function create()
    {
        return view('products.create');
    }


    /**
     * Store Product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' => 'required|string|max:255',

            'category' => 'required|string|max:100',

            'description' => 'nullable|string',

            'price' => 'required|numeric|min:0',

            'quantity' => 'required|integer|min:0',

            // Added for Stock Alert Feature
            'minimum_stock' => 'required|integer|min:0',

            'status' => 'required|in:Active,Inactive',

            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);


        if ($request->hasFile('image')) {

            $validated['image'] = $request
                ->file('image')
                ->store('products', 'public');
        }


        Product::create($validated);


        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }



    /**
     * Show Product.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }



    /**
     * Edit Product.
     */
    public function edit(Product $product)
    {
        return view('products.create', compact('product'));
    }



    /**
     * Update Product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([

            'name' => 'required|string|max:255',

            'category' => 'required|string|max:100',

            'description' => 'nullable|string',

            'price' => 'required|numeric|min:0',

            'quantity' => 'required|integer|min:0',

            // Added for Stock Alert Feature
            'minimum_stock' => 'required|integer|min:0',

            'status' => 'required|in:Active,Inactive',

            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);



        if ($request->hasFile('image')) {

            if ($product->image) {

                Storage::disk('public')->delete($product->image);
            }


            $validated['image'] = $request
                ->file('image')
                ->store('products', 'public');
        }



        $product->update($validated);



        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }



    /**
     * Delete Product.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {

            Storage::disk('public')->delete($product->image);
        }


        $product->delete();


        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }



    /**
     * Remove Product Image.
     */
    public function removeImage(Product $product)
    {
        if ($product->image) {

            Storage::disk('public')->delete($product->image);


            $product->image = null;

            $product->save();
        }


        return back()->with('success', 'Image removed successfully.');
    }

    public function export()
    {
        return Excel::download(
            new ProductsExport,
            'products.xlsx'
        );
    }
}
