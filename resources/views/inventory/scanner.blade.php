@extends('layouts.app')

@section('title', 'Webcam Barcode & QR Scanner')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 bg-emerald-500/20 text-emerald-300 text-xs font-bold px-2.5 py-0.5 rounded-full border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    WEBCAM SCANNER ACTIVE
                </span>
                <span class="text-xs text-white/80">Real-Time Video Stream</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fas fa-camera text-emerald-300"></i> Interactive Webcam Scanner Studio
            </h1>
            <p class="text-white/80 text-sm mt-1">
                Hold product barcode or QR code in front of your camera for instant SKU identification and quick stock audits.
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('inventory.barcodes') }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-barcode mr-1"></i> Barcode Studio
            </a>
            <a href="{{ route('inventory.ledger') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-history mr-1"></i> Stock Ledger
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left: Camera Viewport (7 Cols) --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-gray-900 rounded-2xl shadow-xl overflow-hidden border border-gray-700 relative">
                
                {{-- Video Container --}}
                <div class="relative bg-black flex items-center justify-center min-h-[360px] aspect-video">
                    <video id="scannerVideo" autoplay playsinline class="w-full h-full object-cover"></video>

                    {{-- Target Viewfinder Overlay --}}
                    <div id="targetOverlay" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="relative w-64 h-40 border-2 border-emerald-400/80 rounded-xl shadow-[0_0_15px_rgba(52,211,153,0.3)] flex items-center justify-center">
                            <div class="w-full h-0.5 bg-red-500 shadow-[0_0_8px_red] animate-pulse"></div>
                            <span class="absolute -bottom-6 text-[11px] font-mono text-emerald-300 bg-black/60 px-2 py-0.5 rounded">
                                Align Code Inside Frame
                            </span>
                        </div>
                    </div>

                    {{-- Camera Off State --}}
                    <div id="cameraOffPlaceholder" class="absolute inset-0 bg-gray-900 flex flex-col items-center justify-center p-6 text-center text-gray-400">
                        <i class="fas fa-video-slash text-5xl mb-3 text-gray-600"></i>
                        <p class="font-bold text-white text-base">Camera is currently paused or inactive</p>
                        <p class="text-xs text-gray-400 max-w-sm mt-1">Click the button below to grant permission and initiate your computer or mobile camera stream.</p>
                        <button onclick="startCamera()" class="mt-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-5 py-2.5 rounded-lg shadow-lg text-sm flex items-center gap-2">
                            <i class="fas fa-video"></i> Start Webcam Stream
                        </button>
                    </div>
                </div>

                {{-- Camera Controls Bar --}}
                <div class="p-3 bg-gray-800 border-t border-gray-700 flex flex-wrap justify-between items-center gap-2 text-xs text-gray-300">
                    <div class="flex items-center gap-2">
                        <button id="btnToggleCamera" onclick="toggleCamera()" class="bg-gray-700 hover:bg-gray-600 px-3 py-1.5 rounded text-white font-semibold">
                            <i class="fas fa-pause mr-1"></i> Pause Stream
                        </button>
                        <button onclick="switchCamera()" class="bg-gray-700 hover:bg-gray-600 px-3 py-1.5 rounded text-white font-semibold">
                            <i class="fas fa-sync-alt mr-1"></i> Flip Camera
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" id="chkAudio" checked class="rounded text-emerald-500">
                            Audio Feedback
                        </label>
                    </div>
                </div>
            </div>

            {{-- Quick Manual Simulation Bar (For quick testing without printing barcodes) --}}
            <div class="bg-white rounded-xl shadow p-4 border border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">
                        <i class="fas fa-bolt text-amber-500 mr-1"></i> Fast Barcode Simulator / Manual Scanner Input
                    </span>
                    <span class="text-xs text-gray-400">Click any barcode to test scan</span>
                </div>
                <div class="flex gap-2">
                    <input type="text" id="manualBarcodeInput" placeholder="Enter barcode or SKU (e.g. 890123400001)..." class="flex-1 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                    <button onclick="simulateScan(document.getElementById('manualBarcodeInput').value)" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        Simulate Scan
                    </button>
                </div>

                <div class="flex flex-wrap gap-1.5 mt-3">
                    @foreach($products->take(5) as $quick)
                        <button onclick="simulateScan('{{ $quick->barcode ?? $quick->sku }}')" class="bg-gray-100 hover:bg-emerald-50 hover:text-emerald-700 border text-[11px] font-mono px-2.5 py-1 rounded text-gray-700 transition">
                            {{ $quick->name }} ({{ $quick->barcode ?? $quick->sku }})
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Scan Result & Real-Time Action Drawer (5 Cols) --}}
        <div class="lg:col-span-5 space-y-4">
            <div id="productResultCard" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 min-h-[420px] flex flex-col justify-between">
                
                {{-- Initial Waiting State --}}
                <div id="resultPlaceholder" class="text-center py-16 text-gray-400 my-auto">
                    <div class="w-16 h-16 rounded-full bg-emerald-50 mx-auto flex items-center justify-center text-emerald-500 text-2xl mb-3 shadow-inner">
                        <i class="fas fa-qrcode animate-bounce"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">Ready to Scan</h3>
                    <p class="text-xs text-gray-500 max-w-xs mx-auto mt-1">
                        Point your webcam toward any barcode or click one of the quick simulator badges to see instant product details.
                    </p>
                </div>

                {{-- Scanned Product Content --}}
                <div id="resultContent" class="hidden space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <span id="resCategoryBadge" class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded-full">
                                Category
                            </span>
                            <h2 id="resProductName" class="text-xl font-bold text-gray-900 mt-1">Product Name</h2>
                            <div class="text-xs font-mono text-gray-500">
                                SKU: <span id="resProductSku" class="text-indigo-600 font-semibold"></span> &bull; 
                                Barcode: <span id="resProductBarcode" class="text-gray-700 font-semibold"></span>
                            </div>
                        </div>
                        <span id="resStockBadge" class="text-xs font-bold px-2.5 py-1 rounded-full">
                            In Stock
                        </span>
                    </div>

                    {{-- Price and Supplier Info --}}
                    <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100 text-xs">
                        <div>
                            <span class="text-gray-500 block">Unit Retail Price:</span>
                            <span id="resProductPrice" class="text-lg font-black text-gray-900">$0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Current Inventory:</span>
                            <span id="resProductStock" class="text-lg font-black text-emerald-600">0 units</span>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-gray-200 text-gray-600">
                            <strong>Vendor:</strong> <span id="resSupplierName">Unassigned</span>
                        </div>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="space-y-2 pt-2">
                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wide block">1-Click Live Stock Adjustment</span>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="adjustCurrentStock('increment')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-3 rounded-lg text-xs flex items-center justify-center gap-1.5 shadow">
                                <i class="fas fa-plus-circle"></i> +1 Stock In
                            </button>
                            <button onclick="adjustCurrentStock('decrement')" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-3 rounded-lg text-xs flex items-center justify-center gap-1.5 shadow">
                                <i class="fas fa-minus-circle"></i> -1 Stock Out
                            </button>
                        </div>
                        <a id="resViewDetailsLink" href="#" class="block text-center bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold py-2.5 rounded-lg shadow transition">
                            <i class="fas fa-external-link-alt mr-1"></i> Open Complete Product Details
                        </a>
                    </div>
                </div>

                {{-- Scan Status Alert Footer --}}
                <div id="scanStatusAlert" class="mt-4 p-2.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 text-center">
                    Scanner Ready
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    let activeStream = null;
    let isCameraActive = false;
    let currentFacingMode = 'environment';
    let lastScannedCode = null;
    let currentScannedProductId = null;

    // Web Audio API Synthesizer Beep
    function playScanBeep() {
        if (!document.getElementById('chkAudio').checked) return;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = "sine";
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.12);
        } catch(e) {}
    }

    async function startCamera() {
        try {
            const constraints = {
                video: { facingMode: currentFacingMode }
            };
            activeStream = await navigator.mediaDevices.getUserMedia(constraints);
            const video = document.getElementById('scannerVideo');
            video.srcObject = activeStream;
            document.getElementById('cameraOffPlaceholder').classList.add('hidden');
            isCameraActive = true;
            document.getElementById('btnToggleCamera').innerHTML = '<i class="fas fa-pause mr-1"></i> Pause Stream';
            startBarcodeDetection(video);
        } catch (err) {
            console.error("Camera access failed:", err);
            alert("Could not access camera: " + err.message + ". You can still test with the manual simulator below!");
        }
    }

    function toggleCamera() {
        if (!activeStream) {
            startCamera();
            return;
        }
        const tracks = activeStream.getVideoTracks();
        if (tracks.length > 0) {
            tracks[0].enabled = !tracks[0].enabled;
            isCameraActive = tracks[0].enabled;
            document.getElementById('btnToggleCamera').innerHTML = isCameraActive 
                ? '<i class="fas fa-pause mr-1"></i> Pause Stream' 
                : '<i class="fas fa-play mr-1"></i> Resume Stream';
        }
    }

    function switchCamera() {
        if (activeStream) {
            activeStream.getTracks().forEach(t => t.stop());
        }
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        startCamera();
    }

    // Barcode Detection API Loop (Native in modern Chromium/Edge/Android)
    function startBarcodeDetection(video) {
        if (!('BarcodeDetector' in window)) {
            console.log("BarcodeDetector API not supported in this browser; manual simulation active.");
            return;
        }

        const barcodeDetector = new BarcodeDetector({
            formats: ['code_128', 'qr_code', 'ean_13', 'upc_a']
        });

        const detectFrame = async () => {
            if (isCameraActive && video.readyState === video.HAVE_ENOUGH_DATA) {
                try {
                    const barcodes = await barcodeDetector.detect(video);
                    if (barcodes.length > 0) {
                        const raw = barcodes[0].rawValue;
                        if (raw !== lastScannedCode) {
                            lastScannedCode = raw;
                            playScanBeep();
                            lookupBarcode(raw);
                            setTimeout(() => { lastScannedCode = null; }, 2500);
                        }
                    }
                } catch (e) {}
            }
            requestAnimationFrame(detectFrame);
        };
        detectFrame();
    }

    function simulateScan(code) {
        if (!code) return;
        playScanBeep();
        lookupBarcode(code);
    }

    async function lookupBarcode(code, action = null) {
        const alertEl = document.getElementById('scanStatusAlert');
        alertEl.className = "mt-4 p-2.5 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700 text-center";
        alertEl.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> Querying inventory database for code: <strong>${code}</strong>...`;

        try {
            const resp = await fetch("{{ route('inventory.scanner.lookup') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ code: code, action: action })
            });

            const data = await resp.json();

            if (data.success && data.product) {
                renderProductDetails(data.product);
                alertEl.className = "mt-4 p-2.5 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-800 text-center";
                alertEl.innerHTML = `<i class="fas fa-check-circle mr-1"></i> ${data.message}`;
            } else {
                alertEl.className = "mt-4 p-2.5 rounded-lg text-xs font-medium bg-red-50 text-red-800 text-center";
                alertEl.innerHTML = `<i class="fas fa-times-circle mr-1"></i> ${data.message || 'Product not found.'}`;
            }
        } catch (e) {
            alertEl.className = "mt-4 p-2.5 rounded-lg text-xs font-medium bg-red-50 text-red-800 text-center";
            alertEl.innerHTML = `<i class="fas fa-exclamation-triangle mr-1"></i> Network error during product lookup.`;
        }
    }

    function renderProductDetails(product) {
        currentScannedProductId = product.id;
        document.getElementById('resultPlaceholder').classList.add('hidden');
        document.getElementById('resultContent').classList.remove('hidden');

        document.getElementById('resProductName').textContent = product.name;
        document.getElementById('resProductSku').textContent = product.sku || 'N/A';
        document.getElementById('resProductBarcode').textContent = product.barcode || 'N/A';
        document.getElementById('resProductPrice').textContent = '$' + product.price;
        document.getElementById('resProductStock').textContent = product.quantity + ' units';
        document.getElementById('resCategoryBadge').textContent = product.category;
        document.getElementById('resSupplierName').textContent = product.supplier_name;
        document.getElementById('resViewDetailsLink').href = product.url;

        const badge = document.getElementById('resStockBadge');
        if (product.is_out_of_stock) {
            badge.className = "text-xs font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-800";
            badge.textContent = "OUT OF STOCK";
        } else if (product.is_low_stock) {
            badge.className = "text-xs font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800";
            badge.textContent = "LOW STOCK ALERT";
        } else {
            badge.className = "text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800";
            badge.textContent = "IN STOCK";
        }
    }

    function adjustCurrentStock(action) {
        if (!currentScannedProductId) return;
        lookupBarcode(currentScannedProductId, action);
    }
</script>
@endsection
