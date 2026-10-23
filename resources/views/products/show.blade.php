@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="md:flex">
            <!-- Product Image -->
            <div class="md:w-2/5 bg-gray-100 p-8 flex items-center justify-center">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                    alt="{{ $product->name }}"
                    class="w-full h-auto max-h-96 object-contain rounded-lg">
                @else
                <div class="text-center">
                    <i class="fas fa-box text-8xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No image available</p>
                </div>
                @endif
            </div>

            <!-- Product Details -->
            <div class="md:w-3/5 p-8">
                <!-- Header -->
                <div class="flex justify-between items-start mb-6">
                    <div>
                        @if($product->isOutOfStock())

                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            Out of Stock
                        </span>

                        @elseif($product->isLowStock())

                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Low Stock
                        </span>

                        @else

                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            Available
                        </span>

                        @endif


                        @if($product->status == 'Active')

                        <span class="inline-block ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            Active
                        </span>

                        @else

                        <span class="inline-block ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                            Inactive
                        </span>

                        @endif


                        <h1 class="text-3xl font-bold text-gray-800 mt-2">
                            {{ $product->name }}
                        </h1>
                    </div>
                    <div class="text-3xl font-bold text-indigo-600">
                        ₹{{ number_format($product->price, 2) }}
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Description</h3>
                    <div class="prose max-w-none">
                        {{ $product->description ? nl2br(e($product->description)) : 'No description available.' }}
                    </div>
                </div>

                <!-- Product Info Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">
                            Current Stock
                        </h4>
                        <p class="text-2xl font-bold font-mono {{ $product->quantity <= $product->minimum_stock ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $product->quantity }}
                        </p>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">
                            Min Buffer
                        </h4>
                        <p class="text-2xl font-bold font-mono text-yellow-600">
                            {{ $product->minimum_stock }}
                        </p>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">SKU</h4>
                        <p class="text-sm font-bold font-mono text-indigo-700">{{ $product->sku ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Supplier</h4>
                        @if($product->supplier)
                            <a href="{{ route('suppliers.show', $product->supplier->id) }}" class="text-xs font-bold text-indigo-600 hover:underline block truncate">
                                {{ $product->supplier->name }}
                            </a>
                        @else
                            <span class="text-xs text-gray-400">Unassigned</span>
                        @endif
                    </div>
                </div>

                <!-- Barcode & QR Code Section -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-center sm:text-left flex-1">
                        <span class="text-[11px] font-bold uppercase text-gray-500 block mb-1">Scannable Code-128 Barcode</span>
                        <svg id="productBarcodeSvg" class="mx-auto sm:mx-0"></svg>
                        <div class="text-xs font-mono font-bold text-gray-700 mt-1">{{ $product->barcode ?? $product->sku }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div id="productQrCode" class="bg-white p-1 rounded border shadow-xs"></div>
                        <div class="text-left">
                            <span class="text-xs font-bold text-gray-800 block">2D QR Code</span>
                            <a href="{{ route('inventory.barcodes.print', ['selected_products' => [$product->id], 'repeat' => 4]) }}" target="_blank" class="mt-1 inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-bold">
                                <i class="fas fa-print"></i> Print Stickers
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4 pt-8 border-t border-gray-200">
                    <a href="{{ route('products.edit', $product) }}"
                        class="flex-1 bg-yellow-50 text-yellow-700 border border-yellow-200 py-3 rounded-lg font-medium text-center hover:bg-yellow-100 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Product
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Are you sure you want to delete this product?')"
                            class="w-full bg-red-50 text-red-700 border border-red-200 py-3 rounded-lg font-medium hover:bg-red-100 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="card p-6 text-center">
            <div class="text-4xl text-indigo-600 mb-3">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3 class="font-semibold text-gray-700 mb-1">Created</h3>
            <p class="text-gray-600">{{ $product->created_at->diffForHumans() }}</p>
        </div>

        <div class="card p-6 text-center">
            <div class="text-4xl text-green-600 mb-3">
                <i class="fas fa-sync-alt"></i>
            </div>
            <h3 class="font-semibold text-gray-700 mb-1">Last Updated</h3>
            <p class="text-gray-600">{{ $product->updated_at->diffForHumans() }}</p>
        </div>

        <div class="card p-6 text-center">
            <div class="text-4xl text-purple-600 mb-3">
                <i class="fas fa-hashtag"></i>
            </div>
            <h3 class="font-semibold text-gray-700 mb-1">Product ID</h3>
            <p class="text-gray-600 font-mono">{{ $product->id }}</p>
        </div>
    </div>
    <!-- Stock Movement Audit Trail History -->
    <div class="card p-6 mt-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-600"></i> Stock Movement Audit Ledger
                </h3>
                <p class="text-xs text-gray-500">Historical trail of receipts, dispatches, and adjustments for this SKU.</p>
            </div>
            <a href="{{ route('inventory.ledger', ['product_id' => $product->id]) }}" class="text-xs font-bold text-indigo-600 hover:underline">
                View All in Ledger &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b uppercase font-semibold text-gray-500">
                        <th class="p-2.5">Date</th>
                        <th class="p-2.5">Type</th>
                        <th class="p-2.5 text-center">Previous</th>
                        <th class="p-2.5 text-center">Delta</th>
                        <th class="p-2.5 text-center">Balance</th>
                        <th class="p-2.5">Reason</th>
                        <th class="p-2.5">Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($product->stockMovements as $move)
                        <tr>
                            <td class="p-2.5 text-gray-500 whitespace-nowrap">{{ $move->created_at->format('M d, Y h:i A') }}</td>
                            <td class="p-2.5 font-bold uppercase text-[10px]">
                                <span class="px-2 py-0.5 rounded {{ $move->quantity_change > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ str_replace('_', ' ', $move->type) }}
                                </span>
                            </td>
                            <td class="p-2.5 text-center font-mono">{{ $move->previous_quantity }}</td>
                            <td class="p-2.5 text-center font-mono font-bold {{ $move->quantity_change > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $move->quantity_change > 0 ? '+' : '' }}{{ $move->quantity_change }}
                            </td>
                            <td class="p-2.5 text-center font-mono font-bold text-gray-900">{{ $move->new_quantity }}</td>
                            <td class="p-2.5 text-gray-700">{{ $move->reason ?? 'Stock transaction' }}</td>
                            <td class="p-2.5 font-mono text-gray-400">{{ $move->reference_no }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-400">No stock audit history recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Render 1D Barcode
        try {
            JsBarcode("#productBarcodeSvg", "{{ $product->barcode ?? $product->sku ?? '89000000' }}", {
                format: "CODE128",
                displayValue: false,
                height: 40,
                margin: 0
            });
        } catch(e) {}

        // Render 2D QR Code
        try {
            new QRCode(document.getElementById("productQrCode"), {
                text: "{{ route('products.show', $product->id) }}",
                width: 50,
                height: 50,
                colorDark: "#1e1b4b",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        } catch(e) {}
    });
</script>
@endsection