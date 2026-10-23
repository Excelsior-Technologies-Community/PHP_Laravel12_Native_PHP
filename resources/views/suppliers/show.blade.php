@extends('layouts.app')

@section('title', $supplier->name . ' - Supplier Profile')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Top Bar --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('suppliers.index') }}" class="text-xs text-white/80 hover:text-white flex items-center gap-1">
                    &larr; Back to Suppliers
                </a>
                <span class="text-white/40">&bull;</span>
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded-full">
                    Vendor #{{ $supplier->id }}
                </span>
            </div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fas fa-truck text-indigo-300"></i> {{ $supplier->name }}
            </h1>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('inventory.po.generate', ['supplier_id' => $supplier->id]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-lg text-sm flex items-center gap-2 shadow">
                <i class="fas fa-file-invoice"></i> Draft Supplier PO
            </a>
            <a href="{{ route('suppliers.edit', $supplier->id) }}" class="bg-white text-gray-800 hover:bg-gray-100 font-bold px-4 py-2 rounded-lg text-sm flex items-center gap-1.5 shadow">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
            <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500/80 hover:bg-red-600 text-white font-bold px-3 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Info Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-indigo-600">
            <span class="text-xs font-bold uppercase text-gray-500">Contact Representative</span>
            <div class="text-base font-bold text-gray-900 mt-1">{{ $supplier->contact_person ?? 'Not specified' }}</div>
            <span class="text-xs text-indigo-600 truncate block">{{ $supplier->email ?? 'No email' }}</span>
        </div>

        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-amber-500">
            <span class="text-xs font-bold uppercase text-gray-500">Reliability Score</span>
            <div class="text-2xl font-black text-amber-600 mt-1 flex items-center gap-1">
                <i class="fas fa-star text-amber-400 text-lg"></i> {{ number_format($supplier->reliability_score, 1) }} / 5.0
            </div>
            <span class="text-[11px] text-gray-400">Calculated delivery accuracy</span>
        </div>

        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-emerald-500">
            <span class="text-xs font-bold uppercase text-gray-500">Fulfillment Terms</span>
            <div class="text-base font-bold text-gray-900 mt-1">{{ $supplier->payment_terms }}</div>
            <span class="text-xs text-emerald-700 font-semibold">{{ $supplier->lead_time_days }} days lead time</span>
        </div>

        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-purple-500">
            <span class="text-xs font-bold uppercase text-gray-500">Total Supplied Value</span>
            <div class="text-2xl font-black text-purple-700 mt-1">${{ number_format($totalSuppliedValue, 2) }}</div>
            <span class="text-[11px] text-gray-400">{{ count($supplier->products) }} catalog products</span>
        </div>
    </div>

    {{-- Linked Products Table --}}
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-5 border-b flex justify-between items-center bg-gray-50">
            <div>
                <h2 class="text-base font-bold text-gray-900">Products Sourced From This Supplier</h2>
                <p class="text-xs text-gray-500">Real-time stock indicators for active catalog lines.</p>
            </div>
            <a href="{{ route('products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs flex items-center gap-1">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100/60 border-b text-xs uppercase font-semibold text-gray-500">
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Product Name &amp; SKU</th>
                        <th class="p-3.5">Category</th>
                        <th class="p-3.5 text-right">Retail Price</th>
                        <th class="p-3.5 text-center">Current Stock</th>
                        <th class="p-3.5 text-center">Safety Buffer</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($supplier->products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3.5">
                                @if($product->quantity == 0)
                                    <span class="bg-red-100 text-red-800 text-[11px] font-bold px-2 py-0.5 rounded-full border border-red-200">
                                        OUT OF STOCK
                                    </span>
                                @elseif($product->isLowStock())
                                    <span class="bg-amber-100 text-amber-800 text-[11px] font-bold px-2 py-0.5 rounded-full border border-amber-200">
                                        LOW STOCK
                                    </span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-800 text-[11px] font-bold px-2 py-0.5 rounded-full border border-emerald-200">
                                        HEALTHY
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <a href="{{ route('products.show', $product->id) }}" class="font-bold text-gray-900 hover:text-indigo-600 block">
                                    {{ $product->name }}
                                </a>
                                <span class="font-mono text-xs text-gray-500">{{ $product->sku }}</span>
                            </td>
                            <td class="p-3.5 text-xs text-gray-600">{{ $product->category }}</td>
                            <td class="p-3.5 text-right font-mono font-bold text-gray-900">
                                ${{ number_format($product->price, 2) }}
                            </td>
                            <td class="p-3.5 text-center font-bold font-mono {{ $product->quantity <= $product->minimum_stock ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $product->quantity }}
                            </td>
                            <td class="p-3.5 text-center font-mono text-gray-500">{{ $product->minimum_stock }}</td>
                            <td class="p-3.5 text-right space-x-1">
                                <a href="{{ route('products.show', $product->id) }}" class="text-gray-500 hover:text-indigo-600 p-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product->id) }}" class="text-indigo-600 hover:text-indigo-900 p-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">
                                <i class="fas fa-box-open text-3xl mb-2 text-gray-300"></i>
                                <p>No products currently assigned to this vendor.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
