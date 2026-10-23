<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request): View
    {
        $query = Supplier::withCount('products');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest()->paginate(9)->withQueryString();

        $totalSuppliers = Supplier::count();
        $avgLeadTime = round(Supplier::avg('lead_time_days') ?? 0, 1);
        $topRatedCount = Supplier::where('reliability_score', '>=', 4.5)->count();

        return view('suppliers.index', compact(
            'suppliers',
            'totalSuppliers',
            'avgLeadTime',
            'topRatedCount'
        ));
    }

    /**
     * Show form for creating a new supplier.
     */
    public function create(): View
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'payment_terms' => 'required|string|max:100',
            'lead_time_days' => 'required|integer|min:1|max:365',
            'reliability_score' => 'required|numeric|min:1|max:5',
            'notes' => 'nullable|string',
        ]);

        $supplier = Supplier::create($validated);

        return redirect()->route('suppliers.show', $supplier->id)
            ->with('success', "Supplier '{$supplier->name}' successfully onboarded!");
    }

    /**
     * Display the specified supplier with linked products and low-stock alerts.
     */
    public function show($id): View
    {
        $supplier = Supplier::with(['products' => function ($q) {
            $q->orderBy('quantity');
        }])->findOrFail($id);

        $lowStockCount = $supplier->products->filter->isLowStock()->count();
        $outOfStockCount = $supplier->products->filter->isOutOfStock()->count();
        $totalSuppliedValue = $supplier->products->sum(fn ($p) => $p->price * $p->quantity);

        return view('suppliers.show', compact(
            'supplier',
            'lowStockCount',
            'outOfStockCount',
            'totalSuppliedValue'
        ));
    }

    /**
     * Show form for editing a supplier.
     */
    public function edit($id): View
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'payment_terms' => 'required|string|max:100',
            'lead_time_days' => 'required|integer|min:1|max:365',
            'reliability_score' => 'required|numeric|min:1|max:5',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.show', $supplier->id)
            ->with('success', "Supplier '{$supplier->name}' profile updated successfully!");
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy($id): RedirectResponse
    {
        $supplier = Supplier::findOrFail($id);
        $name = $supplier->name;
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$name}' deleted successfully.");
    }
}
