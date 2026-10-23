<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    /**
     * Stock Movement Audit Ledger.
     */
    public function index(Request $request): View
    {
        $query = StockMovement::with('product');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        $movements = $query->latest()->paginate(15)->withQueryString();

        // Ledger Summary Metrics
        $totalStockIn = StockMovement::where('quantity_change', '>', 0)->sum('quantity_change');
        $totalStockOut = abs(StockMovement::where('quantity_change', '<', 0)->sum('quantity_change'));
        $totalDamaged = abs(StockMovement::where('type', 'damaged')->sum('quantity_change'));
        $totalAudits = StockMovement::where('type', 'audit_adjustment')->count();

        $products = Product::orderBy('name')->get();

        return view('inventory.ledger', compact(
            'movements',
            'totalStockIn',
            'totalStockOut',
            'totalDamaged',
            'totalAudits',
            'products'
        ));
    }

    /**
     * Record a manual stock adjustment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:stock_in,stock_out,damaged,audit_adjustment,return',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'reference_no' => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int) $validated['quantity'];

        // Determine positive vs negative delta
        $delta = match ($validated['type']) {
            'stock_in', 'return' => $qty,
            'stock_out', 'damaged' => -$qty,
            'audit_adjustment' => $qty - $product->quantity, // direct override
        };

        $product->recordMovement(
            $validated['type'],
            $delta,
            $validated['reason'],
            $validated['reference_no'] ?? 'MANUAL-' . strtoupper(uniqid()),
            'Admin / User'
        );

        return redirect()->route('inventory.ledger')
            ->with('success', "Stock movement of {$qty} units successfully recorded for {$product->name}!");
    }

    /**
     * Automated Reorder Watchdog & Low-Stock Dashboard.
     */
    public function reorderWatchdog(): View
    {
        $lowStockProducts = Product::with('supplier')
            ->whereColumn('quantity', '<=', 'minimum_stock')
            ->orderBy('quantity')
            ->get();

        $suppliers = Supplier::withCount('products')->get();

        $outOfStockCount = $lowStockProducts->where('quantity', 0)->count();
        $criticalStockCount = $lowStockProducts->where('quantity', '>', 0)->count();

        return view('inventory.reorder-watchdog', compact(
            'lowStockProducts',
            'suppliers',
            'outOfStockCount',
            'criticalStockCount'
        ));
    }

    /**
     * Generate 1-Click Purchase Order (PO) Draft for low stock items.
     */
    public function generatePurchaseOrder(Request $request): View
    {
        $supplierId = $request->input('supplier_id');
        $supplier = $supplierId ? Supplier::findOrFail($supplierId) : null;

        $query = Product::whereColumn('quantity', '<=', 'minimum_stock');
        if ($supplier) {
            $query->where('supplier_id', $supplier->id);
        }

        $items = $query->get()->map(function ($product) {
            // Suggest replenishment to reach 3x minimum stock or at least +20 units
            $target = max($product->minimum_stock * 3, $product->minimum_stock + 20);
            $recommendedOrder = max(10, $target - $product->quantity);

            return [
                'product' => $product,
                'current_stock' => $product->quantity,
                'minimum_stock' => $product->minimum_stock,
                'recommended_qty' => $recommendedOrder,
                'estimated_cost' => round($recommendedOrder * ($product->price * 0.70), 2), // assumed wholesale cost
            ];
        });

        $poNumber = 'PO-' . date('Ymd') . '-' . rand(100, 999);
        $totalEstimatedCost = collect($items)->sum('estimated_cost');

        return view('inventory.purchase-order', compact(
            'supplier',
            'items',
            'poNumber',
            'totalEstimatedCost'
        ));
    }
}
