<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarcodeScannerController extends Controller
{
    /**
     * Barcode & QR Code Studio - browse and print codes for products.
     */
    public function index(Request $request): View
    {
        $query = Product::with('supplier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->paginate(9)->withQueryString();
        $categories = Product::distinct()->pluck('category')->filter();

        return view('inventory.barcode-studio', compact('products', 'categories'));
    }

    /**
     * Real-Time In-Browser Webcam Scanner Studio.
     */
    public function scanner(): View
    {
        $products = Product::select('id', 'name', 'sku', 'barcode', 'price', 'quantity', 'category')
            ->orderBy('name')
            ->get();

        return view('inventory.scanner', compact('products'));
    }

    /**
     * Printable Thermal & A4 Barcode Sticker Sheet.
     */
    public function printSheet(Request $request): View
    {
        $productIds = $request->input('selected_products');
        
        if (!empty($productIds)) {
            $products = Product::whereIn('id', (array) $productIds)->get();
        } else {
            $products = Product::take(24)->get();
        }

        $repeatCount = (int) $request->input('repeat', 2);
        if ($repeatCount < 1) $repeatCount = 1;
        if ($repeatCount > 10) $repeatCount = 10;

        return view('inventory.barcode-print-sheet', compact('products', 'repeatCount'));
    }

    /**
     * Live API Lookup: Finds a product by Barcode or SKU for webcam scanner.
     */
    public function lookup(Request $request): JsonResponse
    {
        $code = trim($request->input('code', ''));

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a barcode or SKU code.'
            ], 422);
        }

        $product = Product::with('supplier')
            ->where('barcode', $code)
            ->orWhere('sku', $code)
            ->orWhere('id', $code)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => "No product found matching code: {$code}"
            ], 404);
        }

        // Optional quick stock adjustment via scanner
        $action = $request->input('action'); // 'increment', 'decrement', null
        if ($action === 'increment') {
            $product->recordMovement('stock_in', 1, 'Quick Webcam Scan Stock-In', 'SCAN-IN', 'Webcam Scanner');
        } elseif ($action === 'decrement') {
            if ($product->quantity > 0) {
                $product->recordMovement('stock_out', -1, 'Quick Webcam Scan Stock-Out', 'SCAN-OUT', 'Webcam Scanner');
            }
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'price' => number_format($product->price, 2),
                'quantity' => $product->quantity,
                'minimum_stock' => $product->minimum_stock,
                'status' => $product->status,
                'category' => $product->category,
                'supplier_name' => $product->supplier?->name ?? 'Unassigned',
                'is_low_stock' => $product->isLowStock(),
                'is_out_of_stock' => $product->isOutOfStock(),
                'image_url' => $product->image ? asset('storage/' . $product->image) : null,
                'url' => route('products.show', $product->id),
            ],
            'message' => $action ? "Stock updated ({$action}) successfully!" : "Product recognized: {$product->name}"
        ]);
    }
}
