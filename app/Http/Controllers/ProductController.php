<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
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
        $query = Product::with('supplier');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->latest()
            ->paginate(6)
            ->withQueryString();

        $categories = Product::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $suppliers = Supplier::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('products.create', compact('suppliers'));
    }

    /**
     * Store Product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'status' => 'required|in:Active,Inactive',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // Record initial stock movement
        if ($product->quantity > 0) {
            $product->stockMovements()->create([
                'type' => 'stock_in',
                'quantity_change' => $product->quantity,
                'previous_quantity' => 0,
                'new_quantity' => $product->quantity,
                'reason' => 'Initial Inventory Balance',
                'reference_no' => 'INIT-' . $product->id,
                'created_by' => 'Inventory System',
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Show Product.
     */
    public function show(Product $product)
    {
        $product->load(['supplier', 'stockMovements' => fn ($q) => $q->latest()->take(10)]);
        return view('products.show', compact('product'));
    }

    /**
     * Edit Product.
     */
    public function edit(Product $product)
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('products.create', compact('product', 'suppliers'));
    }

    /**
     * Update Product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'status' => 'required|in:Active,Inactive',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $oldQty = $product->quantity;
        $product->update($validated);

        // Record stock movement if quantity changed
        if ($oldQty !== $product->quantity) {
            $delta = $product->quantity - $oldQty;
            $product->stockMovements()->create([
                'type' => $delta > 0 ? 'stock_in' : 'stock_out',
                'quantity_change' => $delta,
                'previous_quantity' => $oldQty,
                'new_quantity' => $product->quantity,
                'reason' => 'Product Edit Adjustment',
                'reference_no' => 'EDIT-' . $product->id,
                'created_by' => 'User / Admin',
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
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
