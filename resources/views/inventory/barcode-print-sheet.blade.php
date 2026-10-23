<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printable Barcode & QR Sticker Sheet - ProductHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
        .sticker-card {
            width: 65mm;
            height: 38mm;
            border: 1px dashed #cbd5e1;
            padding: 4px 6px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fff;
            page-break-inside: avoid;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans p-6 text-gray-900">

    {{-- Print Bar --}}
    <div class="no-print max-w-4xl mx-auto bg-white p-4 rounded-xl shadow-md mb-6 flex justify-between items-center border">
        <div>
            <h1 class="font-bold text-lg text-gray-800">🖨️ Thermal & A4 Barcode Sticker Sheet</h1>
            <p class="text-xs text-gray-500">Total stickers: {{ count($products) * $repeatCount }} &bull; Print preview ready.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2 rounded-lg text-sm flex items-center gap-2 shadow">
                Print Label Sheet Now
            </button>
            <button onclick="window.close()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold px-4 py-2 rounded-lg text-sm">
                Close
            </button>
        </div>
    </div>

    {{-- Labels Grid --}}
    <div class="max-w-[210mm] mx-auto bg-white p-4 shadow-sm border border-gray-200">
        <div class="grid grid-cols-3 gap-3 justify-items-center">
            @foreach($products as $product)
                @for($i = 0; $i < $repeatCount; $i++)
                    <div class="sticker-card rounded shadow-xs text-center">
                        <div class="flex justify-between items-start text-[10px] font-bold text-gray-700 border-b border-gray-100 pb-0.5">
                            <span class="truncate max-w-[40mm]">{{ $product->name }}</span>
                            <span class="text-indigo-600">${{ number_format($product->price, 2) }}</span>
                        </div>

                        {{-- Barcode Element --}}
                        <div class="my-auto">
                            <svg class="barcode-svg mx-auto"
                                 data-value="{{ $product->barcode ?? $product->sku ?? '89000000' }}"
                                 style="max-width: 100%; height: 32px;">
                            </svg>
                            <div class="text-[9px] font-mono font-bold text-gray-600">
                                {{ $product->barcode ?? $product->sku }}
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-[8px] text-gray-500 pt-0.5 border-t border-gray-100">
                            <span>SKU: {{ $product->sku ?? 'PRD-'.$product->id }}</span>
                            <span class="font-semibold uppercase text-indigo-700">{{ $product->category }}</span>
                        </div>
                    </div>
                @endfor
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.barcode-svg').forEach(el => {
                const val = el.getAttribute('data-value');
                if (val) {
                    try {
                        JsBarcode(el, val, {
                            format: "CODE128",
                            displayValue: false,
                            height: 28,
                            margin: 0
                        });
                    } catch(e) {}
                }
            });
        });
    </script>
</body>
</html>
