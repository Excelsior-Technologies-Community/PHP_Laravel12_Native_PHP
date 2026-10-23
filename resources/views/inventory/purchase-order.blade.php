<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order Draft {{ $poNumber }} - ProductHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .shadow-lg { box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans p-6 text-gray-900">

    {{-- Top Action Bar --}}
    <div class="no-print max-w-4xl mx-auto bg-white p-4 rounded-xl shadow-md mb-6 flex justify-between items-center border">
        <div>
            <h1 class="font-bold text-lg text-gray-800">📄 Purchase Order Preview: {{ $poNumber }}</h1>
            <p class="text-xs text-gray-500">Target Supplier: <strong>{{ $supplier?->name ?? 'Consolidated Low-Stock Replenishment' }}</strong></p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2 rounded-lg text-sm flex items-center gap-2 shadow">
                <i class="fas fa-print"></i> Print / Save as PDF
            </button>
            <a href="{{ route('inventory.watchdog') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold px-4 py-2 rounded-lg text-sm flex items-center">
                Return to Watchdog
            </a>
        </div>
    </div>

    {{-- Purchase Order Sheet --}}
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-lg border border-gray-200">
        {{-- PO Header --}}
        <div class="flex justify-between items-start border-b border-gray-200 pb-8 mb-8">
            <div>
                <div class="flex items-center gap-2 text-indigo-600 font-black text-2xl tracking-tight mb-1">
                    <i class="fas fa-cube"></i> ProductHub Logistics
                </div>
                <div class="text-xs text-gray-500 leading-relaxed">
                    100 Innovation Parkway, Suite 400<br>
                    Inventory &amp; Supply Chain Department<br>
                    purchasing@producthub.local &bull; +1 (800) 555-0199
                </div>
            </div>

            <div class="text-right">
                <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full uppercase border border-indigo-100">
                    Purchase Order Draft
                </span>
                <h2 class="text-2xl font-black text-gray-900 mt-2 font-mono">{{ $poNumber }}</h2>
                <div class="text-xs text-gray-500 mt-1">Date: <strong>{{ now()->format('F d, Y') }}</strong></div>
            </div>
        </div>

        {{-- Supplier & Shipping Info --}}
        <div class="grid grid-cols-2 gap-8 mb-8 text-xs">
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <span class="font-bold text-gray-400 uppercase tracking-wider block mb-1">Vendor Information</span>
                <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $supplier?->name ?? 'Multi-Vendor Consolidated Replenishment' }}</h3>
                <div class="text-gray-600 leading-relaxed">
                    Attn: {{ $supplier?->contact_person ?? 'Account Representative' }}<br>
                    Email: {{ $supplier?->email ?? 'procurement@vendor.com' }}<br>
                    Phone: {{ $supplier?->phone ?? 'N/A' }}<br>
                    Payment Terms: <strong>{{ $supplier?->payment_terms ?? 'Net 30' }}</strong> &bull; Lead Time: <strong>{{ $supplier?->lead_time_days ?? 7 }} days</strong>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <span class="font-bold text-gray-400 uppercase tracking-wider block mb-1">Shipping &amp; Delivery Destination</span>
                <h3 class="font-bold text-gray-900 text-sm mb-1">ProductHub Central Fulfillment Center #1</h3>
                <div class="text-gray-600 leading-relaxed">
                    Dock Door 4B, 742 Evergreen Logistics Park<br>
                    San Jose, CA 95134<br>
                    Receiving Hours: Mon-Fri 08:00 - 17:00 PST<br>
                    Required By: <strong>{{ now()->addDays($supplier?->lead_time_days ?? 7)->format('F d, Y') }}</strong>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="overflow-x-auto mb-8">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200 uppercase font-bold text-gray-600">
                        <th class="p-3">#</th>
                        <th class="p-3">Product Description</th>
                        <th class="p-3">SKU</th>
                        <th class="p-3 text-center">Current Stock</th>
                        <th class="p-3 text-center">Safety Buffer</th>
                        <th class="p-3 text-center">Order Qty</th>
                        <th class="p-3 text-right">Est. Unit Wholesale</th>
                        <th class="p-3 text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $index => $item)
                        @php
                            $estUnitWholesale = round($item['product']->price * 0.70, 2);
                        @endphp
                        <tr>
                            <td class="p-3 text-gray-400">{{ $index + 1 }}</td>
                            <td class="p-3 font-bold text-gray-900">{{ $item['product']->name }}</td>
                            <td class="p-3 font-mono text-gray-600">{{ $item['product']->sku }}</td>
                            <td class="p-3 text-center font-mono text-red-600 font-bold">{{ $item['current_stock'] }}</td>
                            <td class="p-3 text-center font-mono text-gray-500">{{ $item['minimum_stock'] }}</td>
                            <td class="p-3 text-center font-mono font-bold text-emerald-700 bg-emerald-50/50">
                                {{ $item['recommended_qty'] }} units
                            </td>
                            <td class="p-3 text-right font-mono">${{ number_format($estUnitWholesale, 2) }}</td>
                            <td class="p-3 text-right font-mono font-bold text-gray-900">
                                ${{ number_format($item['estimated_cost'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-gray-500">
                                No items currently require purchase reordering.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Total & Signatures --}}
        <div class="flex flex-col sm:flex-row justify-between items-start gap-8 pt-4 border-t border-gray-200">
            <div class="text-xs text-gray-500 max-w-sm">
                <span class="font-bold text-gray-800 block mb-1">Procurement Terms:</span>
                All deliverables must reference PO {{ $poNumber }}. Invoices must be submitted in accordance with standard {{ $supplier?->payment_terms ?? 'Net 30' }} agreement.
            </div>

            <div class="w-full sm:w-64 space-y-2 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Estimated Subtotal:</span>
                    <strong class="font-mono text-gray-900">${{ number_format($totalEstimatedCost, 2) }}</strong>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Estimated Freight:</span>
                    <strong class="font-mono text-gray-900">$0.00</strong>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-200">
                    <span>Estimated Total:</span>
                    <span class="font-mono text-emerald-600">${{ number_format($totalEstimatedCost, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-12 mt-12 pt-8 border-t border-dashed border-gray-300 text-xs text-gray-500">
            <div>
                <div class="border-b border-gray-400 w-48 mb-2"></div>
                <span>Authorized Procurement Signature</span>
            </div>
            <div class="text-right">
                <div class="border-b border-gray-400 w-48 ml-auto mb-2"></div>
                <span>Vendor Acknowledgment &amp; Date</span>
            </div>
        </div>
    </div>

</body>
</html>
