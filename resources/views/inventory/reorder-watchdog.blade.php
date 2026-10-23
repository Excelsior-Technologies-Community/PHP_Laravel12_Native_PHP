@extends('layouts.app')

@section('title', 'Low-Stock Reorder Watchdog')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">
                    Automated Watchdog
                </span>
                <span class="text-xs text-white/80">Replenishment Priority Engine</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-amber-300"></i> Low-Stock Reorder Watchdog
            </h1>
            <p class="text-white/80 text-sm mt-1">
                Real-time monitor tracking items at or below safety buffer stock with automated restock calculation.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('inventory.po.generate') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2 text-sm transition">
                <i class="fas fa-file-invoice-dollar"></i> Generate Master Purchase Order
            </a>
            <a href="{{ route('inventory.ledger') }}" class="bg-white text-indigo-700 hover:bg-gray-100 font-bold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2 text-sm transition">
                <i class="fas fa-history"></i> View Stock Ledger
            </a>
        </div>
    </div>

    {{-- Scorecards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500 flex justify-between items-center">
            <div>
                <span class="text-xs font-bold uppercase text-gray-500">Out of Stock (Zero Units)</span>
                <div class="text-2xl font-black text-red-600 mt-0.5">{{ $outOfStockCount }} Products</div>
                <span class="text-[11px] text-gray-400">Immediate fulfillment stall</span>
            </div>
            <i class="fas fa-ban text-3xl text-red-200"></i>
        </div>

        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-amber-500 flex justify-between items-center">
            <div>
                <span class="text-xs font-bold uppercase text-gray-500">Critical Low Stock</span>
                <div class="text-2xl font-black text-amber-600 mt-0.5">{{ $criticalStockCount }} Products</div>
                <span class="text-[11px] text-gray-400">Below minimum safety threshold</span>
            </div>
            <i class="fas fa-exclamation-triangle text-3xl text-amber-200"></i>
        </div>

        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-indigo-500 flex justify-between items-center">
            <div>
                <span class="text-xs font-bold uppercase text-gray-500">Total Action Items</span>
                <div class="text-2xl font-black text-indigo-600 mt-0.5">{{ count($lowStockProducts) }} SKUs</div>
                <span class="text-[11px] text-gray-400">Restock orders pending</span>
            </div>
            <i class="fas fa-boxes text-3xl text-indigo-200"></i>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-100 mb-6">
        <div class="p-4 border-b flex justify-between items-center bg-gray-50">
            <h2 class="font-bold text-gray-800 text-sm">Products Requiring Immediate Reorder</h2>
            <span class="text-xs text-gray-500">Sorted by lowest available stock</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100/70 border-b text-xs uppercase font-semibold text-gray-500">
                        <th class="p-3.5">Urgency</th>
                        <th class="p-3.5">Product Name &amp; SKU</th>
                        <th class="p-3.5">Category</th>
                        <th class="p-3.5 text-center">Current Stock</th>
                        <th class="p-3.5 text-center">Min Threshold</th>
                        <th class="p-3.5 text-center">Suggested Reorder</th>
                        <th class="p-3.5">Assigned Vendor</th>
                        <th class="p-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lowStockProducts as $prod)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3.5">
                                @if($prod->quantity == 0)
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-[11px] font-bold px-2 py-0.5 rounded-full border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> CRITICAL
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 text-[11px] font-bold px-2 py-0.5 rounded-full border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> LOW
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <a href="{{ route('products.show', $prod->id) }}" class="font-bold text-gray-900 hover:text-indigo-600 block">
                                    {{ $prod->name }}
                                </a>
                                <span class="font-mono text-xs text-gray-500">{{ $prod->sku }}</span>
                            </td>
                            <td class="p-3.5 text-xs text-gray-600">{{ $prod->category }}</td>
                            <td class="p-3.5 text-center font-bold font-mono {{ $prod->quantity == 0 ? 'text-red-600' : 'text-amber-600' }}">
                                {{ $prod->quantity }}
                            </td>
                            <td class="p-3.5 text-center font-mono text-gray-600">{{ $prod->minimum_stock }}</td>
                            <td class="p-3.5 text-center font-mono font-bold text-emerald-600">
                                +{{ max(15, ($prod->minimum_stock * 3) - $prod->quantity) }} units
                            </td>
                            <td class="p-3.5 text-xs">
                                @if($prod->supplier)
                                    <a href="{{ route('suppliers.show', $prod->supplier->id) }}" class="font-semibold text-indigo-600 hover:underline">
                                        {{ $prod->supplier->name }}
                                    </a>
                                    <div class="text-[11px] text-gray-400">Lead time: {{ $prod->supplier->lead_time_days }} days</div>
                                @else
                                    <span class="text-gray-400 italic">Unassigned Vendor</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right">
                                @if($prod->supplier_id)
                                    <a href="{{ route('inventory.po.generate', ['supplier_id' => $prod->supplier_id]) }}" class="bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold px-3 py-1.5 rounded-lg text-xs inline-flex items-center gap-1 border border-emerald-200">
                                        <i class="fas fa-file-invoice"></i> Vendor PO
                                    </a>
                                @else
                                    <a href="{{ route('products.edit', $prod->id) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">
                                        Assign Vendor
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-emerald-600">
                                <i class="fas fa-check-circle text-4xl mb-2 text-emerald-300"></i>
                                <h3 class="font-bold text-gray-800 text-base">All Inventory Levels Healthy!</h3>
                                <p class="text-xs text-gray-500 mt-1">No products currently breach their minimum buffer thresholds.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
