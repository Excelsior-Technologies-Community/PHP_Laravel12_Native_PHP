@extends('layouts.app')

@section('title', 'Stock Movement Audit Ledger')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">
                    Audit Trail &amp; Ledger
                </span>
                <span class="text-xs text-white/80">Inventory In &bull; Out &bull; Adjustments</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fas fa-history text-blue-300"></i> Stock Movement Audit Ledger
            </h1>
            <p class="text-white/80 text-sm mt-1">
                Traceable ledger recording every quantity delta, replenishment, sales dispatch, and inventory audit.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('inventory.watchdog') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2 text-sm transition">
                <i class="fas fa-triangle-exclamation"></i> Reorder Watchdog
            </a>
            <button onclick="document.getElementById('adjustmentModal').classList.remove('hidden')" class="bg-white text-indigo-700 hover:bg-gray-100 font-bold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2 text-sm transition">
                <i class="fas fa-plus-circle"></i> Record Stock Movement
            </button>
        </div>
    </div>

    {{-- Ledger Metric Scorecards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-emerald-500">
            <span class="text-xs font-bold uppercase text-gray-500">Total Stock Received</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">+{{ number_format($totalStockIn) }}</div>
            <span class="text-[11px] text-gray-400">Restocked &amp; returned units</span>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-amber-500">
            <span class="text-xs font-bold uppercase text-gray-500">Total Stock Dispatched</span>
            <div class="text-2xl font-black text-amber-600 mt-1">-{{ number_format($totalStockOut) }}</div>
            <span class="text-[11px] text-gray-400">Sales &amp; fulfilled orders</span>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
            <span class="text-xs font-bold uppercase text-gray-500">Damaged / Shrinkage</span>
            <div class="text-2xl font-black text-red-600 mt-1">{{ number_format($totalDamaged) }}</div>
            <span class="text-[11px] text-gray-400">Written off inventory loss</span>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-indigo-500">
            <span class="text-xs font-bold uppercase text-gray-500">Manual Audits Recorded</span>
            <div class="text-2xl font-black text-indigo-600 mt-1">{{ number_format($totalAudits) }}</div>
            <span class="text-[11px] text-gray-400">Physical stock reconciliations</span>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form action="{{ route('inventory.ledger') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search reason or ref #..." class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <select name="product_id" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Products</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->sku }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="type" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Movement Types</option>
                    <option value="stock_in" {{ request('type') == 'stock_in' ? 'selected' : '' }}>Stock In (Restock)</option>
                    <option value="stock_out" {{ request('type') == 'stock_out' ? 'selected' : '' }}>Stock Out (Sale)</option>
                    <option value="damaged" {{ request('type') == 'damaged' ? 'selected' : '' }}>Damaged / Waste</option>
                    <option value="audit_adjustment" {{ request('type') == 'audit_adjustment' ? 'selected' : '' }}>Audit Adjustment</option>
                    <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Customer Return</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg text-sm flex-1">
                    Apply Filter
                </button>
                <a href="{{ route('inventory.ledger') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-3 py-2 rounded-lg text-sm">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Ledger Table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b text-xs uppercase font-semibold text-gray-500">
                        <th class="p-3.5">Timestamp</th>
                        <th class="p-3.5">Product &amp; SKU</th>
                        <th class="p-3.5">Movement Type</th>
                        <th class="p-3.5 text-center">Previous</th>
                        <th class="p-3.5 text-center">Quantity Delta</th>
                        <th class="p-3.5 text-center">New Balance</th>
                        <th class="p-3.5">Reason &amp; Ref #</th>
                        <th class="p-3.5">Auditor / Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($movements as $m)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3.5 text-gray-500 text-xs whitespace-nowrap">
                                <div>{{ $m->created_at->format('M d, Y') }}</div>
                                <div class="text-[11px] text-gray-400">{{ $m->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="p-3.5 font-medium text-gray-900">
                                <a href="{{ route('products.show', $m->product_id) }}" class="hover:text-indigo-600 font-bold block">
                                    {{ $m->product?->name ?? 'Deleted Product' }}
                                </a>
                                <span class="font-mono text-xs text-gray-500">{{ $m->product?->sku ?? 'NO-SKU' }}</span>
                            </td>
                            <td class="p-3.5">
                                @php
                                    $badgeStyle = match($m->type) {
                                        'stock_in' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'return' => 'bg-teal-100 text-teal-800 border-teal-300',
                                        'stock_out' => 'bg-amber-100 text-amber-800 border-amber-300',
                                        'damaged' => 'bg-red-100 text-red-800 border-red-300',
                                        'audit_adjustment' => 'bg-purple-100 text-purple-800 border-purple-300',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="inline-block text-[11px] font-bold uppercase px-2.5 py-0.5 rounded-full border {{ $badgeStyle }}">
                                    {{ str_replace('_', ' ', $m->type) }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center font-mono text-gray-600">{{ $m->previous_quantity }}</td>
                            <td class="p-3.5 text-center font-bold font-mono {{ $m->quantity_change > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $m->quantity_change > 0 ? '+' : '' }}{{ $m->quantity_change }}
                            </td>
                            <td class="p-3.5 text-center font-mono font-bold text-gray-900">{{ $m->new_quantity }}</td>
                            <td class="p-3.5 text-xs">
                                <div class="text-gray-800 font-medium">{{ $m->reason ?? 'Standard stock transaction' }}</div>
                                <div class="text-[11px] font-mono text-gray-400">{{ $m->reference_no }}</div>
                            </td>
                            <td class="p-3.5 text-xs text-gray-600 font-medium">
                                <i class="fas fa-user-circle mr-1 text-gray-400"></i>{{ $m->created_by }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500">
                                <i class="fas fa-box-open text-3xl mb-2 text-gray-300"></i>
                                <p>No stock movement audit records found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t">
            {{ $movements->links() }}
        </div>
    </div>

</div>

{{-- Record Stock Movement Modal --}}
<div id="adjustmentModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
        <button onclick="document.getElementById('adjustmentModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-lg">
            &times;
        </button>

        <h3 class="text-xl font-bold text-gray-900 mb-1 flex items-center gap-2">
            <i class="fas fa-exchange-alt text-indigo-600"></i> Record Stock Movement
        </h3>
        <p class="text-xs text-gray-500 mb-4">Post an auditable quantity delta directly to the central inventory ledger.</p>

        <form action="{{ route('inventory.ledger.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Target Product</label>
                <select name="product_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Product...</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->name }} (Current: {{ $prod->quantity }} units)</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Adjustment Type</label>
                    <select name="type" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="stock_in">Stock In (+)</option>
                        <option value="stock_out">Stock Out (-)</option>
                        <option value="damaged">Damaged (-)</option>
                        <option value="return">Customer Return (+)</option>
                        <option value="audit_adjustment">Audit Override</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Quantity</label>
                    <input type="number" name="quantity" min="1" required placeholder="Units" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Reason / Notes</label>
                <input type="text" name="reason" required placeholder="e.g. Shipment batch received, showroom display" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Reference # (Optional)</label>
                <input type="text" name="reference_no" placeholder="e.g. PO-9840, INV-2026" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg flex-1 text-sm shadow">
                    Save to Ledger
                </button>
                <button type="button" onclick="document.getElementById('adjustmentModal').classList.add('hidden')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-4 rounded-lg text-sm">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
