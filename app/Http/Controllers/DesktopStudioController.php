<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DesktopStudioController extends Controller
{
    /**
     * Native Desktop Hub & OS Notification Radar Studio.
     */
    public function index(): View
    {
        $lowStockCount = Product::whereColumn('quantity', '<=', 'minimum_stock')->count();
        $totalProducts = Product::count();
        $totalSuppliers = Supplier::count();
        $recentMovements = StockMovement::with('product')->latest()->take(5)->get();

        // System diagnostics
        $systemInfo = [
            'os' => PHP_OS_FAMILY,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database' => config('database.default'),
            'server_time' => now()->toDayDateTimeString(),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
        ];

        return view('desktop.studio', compact(
            'lowStockCount',
            'totalProducts',
            'totalSuppliers',
            'recentMovements',
            'systemInfo'
        ));
    }

    /**
     * Global Spotlight Search API (Invoked via Ctrl+K / Ctrl+F).
     */
    public function spotlightSearch(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));

        if (strlen($query) < 1) {
            return response()->json([
                'products' => [],
                'suppliers' => [],
                'quick_actions' => [
                    ['title' => 'Add New Product', 'url' => route('products.create'), 'icon' => 'fa-plus', 'category' => 'Action'],
                    ['title' => 'Barcode Scanner Studio', 'url' => route('inventory.scanner'), 'icon' => 'fa-camera', 'category' => 'Studio'],
                    ['title' => 'Stock Movement Ledger', 'url' => route('inventory.ledger'), 'icon' => 'fa-history', 'category' => 'Inventory'],
                    ['title' => 'Low-Stock Watchdog', 'url' => route('inventory.watchdog'), 'icon' => 'fa-triangle-exclamation', 'category' => 'Alerts'],
                    ['title' => 'Suppliers Directory', 'url' => route('suppliers.index'), 'icon' => 'fa-truck', 'category' => 'Vendors'],
                ]
            ]);
        }

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->take(6)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->name,
                    'subtitle' => "SKU: {$p->sku} • {$p->category} • Stock: {$p->quantity}",
                    'url' => route('products.show', $p->id),
                    'price' => '$' . number_format($p->price, 2),
                    'is_low' => $p->isLowStock(),
                    'is_out' => $p->isOutOfStock(),
                    'icon' => 'fa-box',
                ];
            });

        $suppliers = Supplier::where('name', 'like', "%{$query}%")
            ->orWhere('contact_person', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->take(3)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'title' => $s->name,
                    'subtitle' => "Contact: {$s->contact_person} • {$s->email}",
                    'url' => route('suppliers.show', $s->id),
                    'rating' => $s->reliability_score,
                    'icon' => 'fa-truck',
                ];
            });

        return response()->json([
            'products' => $products,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Diagnostic Notification Radar Test API.
     */
    public function testNotification(Request $request): JsonResponse
    {
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'minimum_stock')->get(['id', 'name', 'quantity', 'minimum_stock']);

        return response()->json([
            'status' => 'success',
            'title' => 'ProductHub Desktop Notification',
            'body' => $lowStockProducts->count() > 0 
                ? "Warning: {$lowStockProducts->count()} products require immediate restocking!" 
                : "All product inventory levels are healthy.",
            'icon' => '/favicon.ico',
            'timestamp' => now()->toIso8601String(),
            'low_stock_count' => $lowStockProducts->count(),
            'items' => $lowStockProducts,
        ]);
    }
}
