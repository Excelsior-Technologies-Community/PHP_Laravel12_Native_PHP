@extends('layouts.app')

@section('title', 'Barcode & QR Code Studio')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">
                    Auto-Generation Studio
                </span>
                <span class="text-xs text-white/80">Code-128 &bull; 2D QR Code</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fas fa-barcode text-yellow-300"></i> Barcode &amp; QR Code Studio
            </h1>
            <p class="text-white/80 text-sm mt-1">
                Dynamically render and print scannable Code-128 barcodes and QR codes for instant warehouse scanning.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('inventory.scanner') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fas fa-camera"></i> Open Webcam Scanner
            </a>
            <button onclick="document.getElementById('batchPrintForm').submit()" class="bg-white text-indigo-700 hover:bg-gray-100 font-semibold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2 transition">
                <i class="fas fa-print"></i> Print Selected Labels
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form action="{{ route('inventory.barcodes') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, SKU or barcode..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
            </div>
            <div>
                <select name="category" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex-1">
                    Filter
                </button>
                <a href="{{ route('inventory.barcodes') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-sm font-semibold">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Products Grid --}}
    <form id="batchPrintForm" action="{{ route('inventory.barcodes.print') }}" method="GET" target="_blank">
        <div class="flex justify-between items-center bg-indigo-900/40 text-white px-4 py-2 rounded-lg mb-4 text-xs font-medium">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)" class="rounded text-indigo-600 focus:ring-indigo-500">
                Select All Products for Batch Label Printing
            </label>
            <div class="flex items-center gap-2">
                <span>Sticker Repeat:</span>
                <select name="repeat" class="bg-white text-gray-800 text-xs rounded px-2 py-1">
                    <option value="1">1 Sticker each</option>
                    <option value="2" selected>2 Stickers each</option>
                    <option value="4">4 Stickers each</option>
                    <option value="8">8 Stickers each</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($products as $product)
                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition">
                    <div class="p-4 border-b bg-gray-50 flex justify-between items-start">
                        <div class="flex items-start gap-2">
                            <input type="checkbox" name="selected_products[]" value="{{ $product->id }}" class="product-checkbox mt-1 rounded text-indigo-600">
                            <div>
                                <h3 class="font-bold text-gray-900 text-base leading-snug">{{ $product->name }}</h3>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    <span class="font-mono font-semibold text-indigo-600">{{ $product->sku ?? 'NO-SKU' }}</span> &bull; {{ $product->category }}
                                </div>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded {{ $product->quantity <= $product->minimum_stock ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $product->quantity }} in stock
                        </span>
                    </div>

                    <div class="p-4 space-y-4">
                        {{-- Barcode Element --}}
                        <div class="bg-gray-50 p-3 rounded-lg border border-dashed border-gray-300 text-center">
                            <svg class="barcode-svg mx-auto"
                                 data-value="{{ $product->barcode ?? $product->sku ?? '89000000' }}"
                                 style="max-width: 100%; height: 50px;">
                            </svg>
                            <div class="text-[11px] font-mono text-gray-600 mt-1 font-bold tracking-wider">
                                {{ $product->barcode ?? $product->sku }}
                            </div>
                        </div>

                        {{-- QR Code Element --}}
                        <div class="flex items-center gap-3 bg-indigo-50/50 p-2.5 rounded-lg">
                            <div class="qr-canvas-wrapper bg-white p-1 rounded border shadow-sm" data-qr="{{ route('products.show', $product->id) }}"></div>
                            <div class="text-xs text-gray-600 flex-1">
                                <span class="font-bold text-gray-800 block text-xs">2D QR Fast Lookup</span>
                                <span class="text-[11px] text-gray-500">Scan via camera to open item detail sheet directly.</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-2.5 bg-gray-50 border-t flex justify-between items-center text-xs">
                        <span class="text-gray-500 font-medium">Price: <strong class="text-gray-900">${{ number_format($product->price, 2) }}</strong></span>
                        <a href="{{ route('inventory.barcodes.print', ['selected_products' => [$product->id], 'repeat' => 4]) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                            <i class="fas fa-print"></i> Single Print (4x)
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white p-8 rounded-xl text-center text-gray-500">
                    <i class="fas fa-box-open text-4xl mb-2 text-gray-300"></i>
                    <p>No products found matching your filter criteria.</p>
                </div>
            @endforelse
        </div>
    </form>

    <div class="mt-6">
        {{ $products->links() }}
    </div>

</div>

{{-- Barcode & QR Code Rendering Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Render 1D Barcodes
        document.querySelectorAll('.barcode-svg').forEach(el => {
            const val = el.getAttribute('data-value');
            if (val) {
                try {
                    JsBarcode(el, val, {
                        format: "CODE128",
                        displayValue: false,
                        height: 45,
                        margin: 0
                    });
                } catch(e) {
                    console.warn("Barcode rendering fallback:", e);
                }
            }
        });

        // Render 2D QR Codes
        document.querySelectorAll('.qr-canvas-wrapper').forEach(el => {
            const text = el.getAttribute('data-qr');
            el.innerHTML = '';
            new QRCode(el, {
                text: text,
                width: 54,
                height: 54,
                colorDark: "#1e1b4b",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        });
    });

    function toggleSelectAll(master) {
        document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = master.checked);
    }
</script>
@endsection
