@extends('layouts.app')

@section('title', 'Native Desktop Studio & OS Notification Radar')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">
                    Native Desktop Engine
                </span>
                <span class="text-xs text-white/80">OS Push Radar &bull; Hotkeys &bull; System Tray</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fas fa-desktop text-purple-300"></i> Native Desktop Integration &amp; OS Radar
            </h1>
            <p class="text-white/80 text-sm mt-1">
                Bridges ProductHub web architecture into an OS-native desktop environment with push notifications and global hotkeys.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <button onclick="requestNotificationPermission()" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2 text-sm transition">
                <i class="fas fa-bell"></i> Enable OS Push Notifications
            </button>
            <button onclick="triggerDesktopNotificationTest()" class="bg-white text-indigo-700 hover:bg-gray-100 font-bold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2 text-sm transition">
                <i class="fas fa-satellite-dish"></i> Ping Notification Radar
            </button>
        </div>
    </div>

    {{-- Simulated Native Desktop Status Bar (Window Header) --}}
    <div class="bg-gray-900 rounded-xl shadow-2xl border border-gray-800 p-4 mb-6 text-white">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                    <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                </div>
                <div class="h-4 w-px bg-gray-700"></div>
                <div class="text-xs font-mono text-gray-300">
                    <i class="fas fa-cube text-indigo-400 mr-1"></i> ProductHub Desktop Client &bull; v1.4.0
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-gray-800 border border-gray-700 text-emerald-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Engine: {{ $systemInfo['os'] }} (PHP {{ $systemInfo['php_version'] }})
                </span>
                <span class="px-2.5 py-1 rounded bg-gray-800 border border-gray-700 text-gray-300 font-mono">
                    RAM: {{ $systemInfo['memory_usage'] }}
                </span>
                <span class="px-2.5 py-1 rounded bg-gray-800 border border-gray-700 text-indigo-300 font-mono">
                    DB: {{ strtoupper($systemInfo['database']) }}
                </span>
            </div>
        </div>
    </div>

    {{-- 2 Columns Grid: Left Hotkeys & Spotlight, Right Notification Radar & Diagnostics --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left: Desktop Hotkeys & Spotlight (7 Cols) --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- Global Hotkeys Card --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-keyboard text-indigo-600"></i> Global Desktop Hotkeys
                        </h2>
                        <p class="text-xs text-gray-500">Accessible everywhere in the application for lightning-fast workflows.</p>
                    </div>
                    <span class="text-[11px] font-bold bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-full border border-indigo-100">
                        Active Listener
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl hover:bg-indigo-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold">
                                <i class="fas fa-search"></i>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-gray-800 block">Spotlight Product &amp; Action Search</span>
                                <span class="text-xs text-gray-500">Instant modal search across all SKUs, categories, and vendors</span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <kbd class="px-2 py-1 bg-white border border-gray-300 rounded shadow-xs text-xs font-mono font-bold text-gray-700">Ctrl</kbd>
                            <span class="text-gray-400 self-center">+</span>
                            <kbd class="px-2 py-1 bg-white border border-gray-300 rounded shadow-xs text-xs font-mono font-bold text-gray-700">K</kbd>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl hover:bg-indigo-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-bold">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-gray-800 block">Add New Product Form</span>
                                <span class="text-xs text-gray-500">Jump directly to the product creation wizard</span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <kbd class="px-2 py-1 bg-white border border-gray-300 rounded shadow-xs text-xs font-mono font-bold text-gray-700">Ctrl</kbd>
                            <span class="text-gray-400 self-center">+</span>
                            <kbd class="px-2 py-1 bg-white border border-gray-300 rounded shadow-xs text-xs font-mono font-bold text-gray-700">N</kbd>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl hover:bg-indigo-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-sm font-bold">
                                <i class="fas fa-camera"></i>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-gray-800 block">Barcode Webcam Scanner</span>
                                <span class="text-xs text-gray-500">Launch real-time camera viewfinder studio</span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <kbd class="px-2 py-1 bg-white border border-gray-300 rounded shadow-xs text-xs font-mono font-bold text-gray-700">Ctrl</kbd>
                            <span class="text-gray-400 self-center">+</span>
                            <kbd class="px-2 py-1 bg-white border border-gray-300 rounded shadow-xs text-xs font-mono font-bold text-gray-700">B</kbd>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl hover:bg-indigo-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">
                                <i class="fas fa-history"></i>
                            </div>
                            <div>
                                <span class="font-bold text-sm text-gray-800 block">Stock Audit Movement Ledger</span>
                                <span class="text-xs text-gray-500">Open complete audit trail history</span>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <kbd class="px-2 py-1 bg-white border border-gray-300 rounded shadow-xs text-xs font-mono font-bold text-gray-700">Ctrl</kbd>
                            <span class="text-gray-400 self-center">+</span>
                            <kbd class="px-2 py-1 bg-white border border-gray-300 rounded shadow-xs text-xs font-mono font-bold text-gray-700">L</kbd>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t flex justify-between items-center">
                    <span class="text-xs text-gray-500">Press <kbd class="px-1.5 py-0.5 bg-gray-100 border rounded text-[11px] font-mono">?</kbd> anywhere to open shortcut guide.</span>
                    <button onclick="openSpotlightModal()" class="text-indigo-600 hover:text-indigo-800 font-bold text-xs flex items-center gap-1">
                        Try Spotlight Search Now &rarr;
                    </button>
                </div>
            </div>
        </div>

        {{-- Right: OS Notification Radar & Live Stream (5 Cols) --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-satellite-dish text-emerald-500"></i> OS Notification Radar
                    </h2>
                    <span id="notificationPermissionBadge" class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-600">
                        Checking...
                    </span>
                </div>
                <p class="text-xs text-gray-500 mb-4">
                    ProductHub watches your database in the background and sends native OS notification toasts whenever inventory depletes past threshold limits.
                </p>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-xs space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Radar Status:</span>
                        <span class="font-bold text-emerald-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> ONLINE &amp; LISTENING
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Active Low Stock Flag:</span>
                        <strong class="{{ $lowStockCount > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $lowStockCount }} items</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Tracked SKUs:</span>
                        <strong class="text-gray-800">{{ $totalProducts }} products</strong>
                    </div>
                </div>

                <button onclick="triggerDesktopNotificationTest()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-lg text-xs shadow flex items-center justify-center gap-2 transition">
                    <i class="fas fa-bell"></i> Send Test OS Desktop Notification
                </button>
            </div>

            {{-- Recent Activity Log --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3 flex items-center gap-2">
                    <i class="fas fa-clock text-gray-400"></i> Recent Inventory Activities
                </h3>
                <div class="space-y-2.5">
                    @forelse($recentMovements as $rm)
                        <div class="text-xs p-2.5 bg-gray-50 rounded-lg flex justify-between items-center">
                            <div>
                                <span class="font-bold text-gray-800 block">{{ $rm->product?->name }}</span>
                                <span class="text-[11px] text-gray-500">{{ $rm->reason ?? 'Stock adjustment' }}</span>
                            </div>
                            <span class="font-mono font-bold {{ $rm->quantity_change > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $rm->quantity_change > 0 ? '+' : '' }}{{ $rm->quantity_change }}
                            </span>
                        </div>
                    @empty
                        <div class="text-xs text-gray-400 text-center py-4">No recent activity</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        updatePermissionBadge();
    });

    function updatePermissionBadge() {
        const badge = document.getElementById('notificationPermissionBadge');
        if (!("Notification" in window)) {
            badge.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-red-100 text-red-700";
            badge.textContent = "Unsupported in Browser";
            return;
        }

        if (Notification.permission === "granted") {
            badge.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700";
            badge.textContent = "OS Push Enabled";
        } else if (Notification.permission === "denied") {
            badge.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-red-100 text-red-700";
            badge.textContent = "Permission Blocked";
        } else {
            badge.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700";
            badge.textContent = "Needs Permission";
        }
    }

    async function requestNotificationPermission() {
        if (!("Notification" in window)) {
            alert("This browser does not support desktop notifications.");
            return;
        }
        const perm = await Notification.requestPermission();
        updatePermissionBadge();
        if (perm === "granted") {
            new Notification("ProductHub Desktop", {
                body: "Native Desktop Notifications are now active!",
                icon: "https://cdn-icons-png.flaticon.com/512/679/679720.png"
            });
        }
    }

    async function triggerDesktopNotificationTest() {
        try {
            const resp = await fetch("{{ route('api.test.notification') }}");
            const data = await resp.json();

            if (Notification.permission === "granted") {
                new Notification(data.title, {
                    body: data.body,
                    icon: "https://cdn-icons-png.flaticon.com/512/679/679720.png"
                });
            } else {
                alert(`[Desktop Notification Demo]\n\n${data.title}\n${data.body}\n\nTip: Click "Enable OS Push Notifications" to receive system tray popups!`);
            }
        } catch(e) {
            console.error("Test notification failed:", e);
        }
    }
</script>
@endsection
