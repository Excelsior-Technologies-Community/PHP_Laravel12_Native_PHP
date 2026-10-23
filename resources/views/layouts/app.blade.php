<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Laravel Simple Project')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #10b981;
            --accent-color: #f59e0b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), #7c3aed);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.4);
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            border-radius: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="navbar fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600 flex items-center">
                        <i class="fas fa-cube mr-2"></i>ProductHub
                    </a>

                    <div class="hidden lg:flex space-x-5 text-sm">
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 font-medium flex items-center">
                            <i class="fas fa-home mr-1.5 text-gray-400"></i>Home
                        </a>
                        <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium flex items-center">
                            <i class="fas fa-box mr-1.5 text-gray-400"></i>Products
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="text-gray-700 hover:text-indigo-600 font-medium flex items-center">
                            <i class="fas fa-truck mr-1.5 text-indigo-500"></i>Suppliers
                        </a>
                        <a href="{{ route('inventory.ledger') }}" class="text-gray-700 hover:text-indigo-600 font-medium flex items-center">
                            <i class="fas fa-history mr-1.5 text-blue-500"></i>Stock Ledger
                        </a>
                        <a href="{{ route('inventory.barcodes') }}" class="text-gray-700 hover:text-indigo-600 font-medium flex items-center">
                            <i class="fas fa-barcode mr-1.5 text-amber-500"></i>Barcodes
                        </a>
                        <a href="{{ route('desktop.studio') }}" class="text-gray-700 hover:text-indigo-600 font-medium flex items-center">
                            <i class="fas fa-desktop mr-1.5 text-purple-500"></i>Desktop Hub
                        </a>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    {{-- Spotlight Search Quick Trigger --}}
                    <button onclick="openSpotlightModal()" class="hidden sm:flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-200 transition">
                        <i class="fas fa-search text-gray-400"></i>
                        <span>Search...</span>
                        <kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded text-[10px] font-mono text-gray-500">Ctrl K</kbd>
                    </button>

                    <a href="{{ route('products.create') }}" class="btn-primary text-xs flex items-center py-2 px-3">
                        <i class="fas fa-plus mr-1"></i>Add Product
                    </a>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-xs bg-gray-100 text-gray-700 px-3 py-2 rounded-lg font-semibold hover:bg-gray-200">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium text-xs">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary text-xs py-2 px-3">
                                    Register
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16 flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-2xl font-bold mb-2">ProductHub</h3>
                    <p class="text-gray-400">Simple Laravel CRUD Application</p>
                </div>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} ProductHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Desktop Spotlight Search Modal (Ctrl+K / Ctrl+F) -->
    <div id="spotlightModal" class="hidden fixed inset-0 bg-gray-950/70 backdrop-blur-xs z-50 flex items-start justify-center pt-20 px-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden border border-gray-200">
            <div class="p-3 border-b flex items-center gap-2 bg-gray-50">
                <i class="fas fa-search text-gray-400 ml-2"></i>
                <input type="text" id="spotlightInput" placeholder="Type a product, SKU, vendor or command..." class="w-full bg-transparent border-none outline-none text-sm font-medium text-gray-800 placeholder-gray-400 focus:ring-0">
                <kbd class="px-2 py-0.5 bg-gray-200 border rounded text-[10px] font-mono text-gray-600">ESC</kbd>
            </div>

            <div id="spotlightResults" class="p-3 max-h-80 overflow-y-auto space-y-2">
                <div class="text-xs text-gray-400 text-center py-6">
                    <p class="font-bold text-gray-600">Quick Commands &amp; Product Search</p>
                    <p class="text-[11px] mt-1">Start typing to search products, barcodes, or suppliers.</p>
                </div>
            </div>

            <div class="px-4 py-2.5 bg-gray-100/70 border-t flex justify-between items-center text-[11px] text-gray-500 font-medium">
                <div class="flex items-center gap-2">
                    <span>Navigation:</span>
                    <kbd class="px-1.5 py-0.5 bg-white border rounded">↑</kbd>
                    <kbd class="px-1.5 py-0.5 bg-white border rounded">↓</kbd>
                    <span>to navigate</span>
                    <kbd class="px-1.5 py-0.5 bg-white border rounded">↵</kbd>
                    <span>to select</span>
                </div>
                <span>ProductHub Desktop Engine</span>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Simple animations
        document.addEventListener('DOMContentLoaded', function() {
            // Fade in animation
            const elements = document.querySelectorAll('.card');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Form validation
            const forms = document.querySelectorAll('form[method="POST"]');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                        submitBtn.disabled = true;
                    }
                });
            });

            // Global Desktop Keyboard Shortcuts
            window.addEventListener('keydown', function(e) {
                // Ignore if user is already typing in an input (except for Esc or Ctrl shortcuts)
                const inInput = ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName);

                if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K' || e.key === 'f' || e.key === 'F')) {
                    e.preventDefault();
                    toggleSpotlightModal();
                } else if ((e.ctrlKey || e.metaKey) && (e.key === 'n' || e.key === 'N')) {
                    e.preventDefault();
                    window.location.href = "{{ route('products.create') }}";
                } else if ((e.ctrlKey || e.metaKey) && (e.key === 'b' || e.key === 'B')) {
                    e.preventDefault();
                    window.location.href = "{{ route('inventory.scanner') }}";
                } else if ((e.ctrlKey || e.metaKey) && (e.key === 'l' || e.key === 'L')) {
                    e.preventDefault();
                    window.location.href = "{{ route('inventory.ledger') }}";
                } else if (e.key === 'Escape') {
                    closeSpotlightModal();
                }
            });

            // Real-time Spotlight live input debounce
            const searchInput = document.getElementById('spotlightInput');
            let debounceTimer;
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        querySpotlight(this.value);
                    }, 200);
                });
            }
        });

        function toggleSpotlightModal() {
            const modal = document.getElementById('spotlightModal');
            if (modal.classList.contains('hidden')) {
                openSpotlightModal();
            } else {
                closeSpotlightModal();
            }
        }

        function openSpotlightModal() {
            const modal = document.getElementById('spotlightModal');
            modal.classList.remove('hidden');
            const input = document.getElementById('spotlightInput');
            input.value = '';
            input.focus();
            querySpotlight('');
        }

        function closeSpotlightModal() {
            document.getElementById('spotlightModal').classList.add('hidden');
        }

        async function querySpotlight(term) {
            const resultsBox = document.getElementById('spotlightResults');
            try {
                const resp = await fetch("{{ route('api.spotlight.search') }}?q=" + encodeURIComponent(term));
                const data = await resp.json();

                let html = '';

                // Quick actions
                if (data.quick_actions && data.quick_actions.length > 0) {
                    html += '<div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider px-2 pt-1 pb-1">Quick Shortcuts</div>';
                    data.quick_actions.forEach(qa => {
                        html += `
                            <a href="${qa.url}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-50 text-gray-700 transition group">
                                <div class="w-6 h-6 rounded bg-gray-100 text-gray-600 group-hover:bg-indigo-600 group-hover:text-white flex items-center justify-center text-xs">
                                    <i class="fas ${qa.icon}"></i>
                                </div>
                                <span class="text-xs font-bold">${qa.title}</span>
                                <span class="ml-auto text-[10px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded">${qa.category}</span>
                            </a>
                        `;
                    });
                }

                // Products
                if (data.products && data.products.length > 0) {
                    html += '<div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider px-2 pt-2 pb-1">Products</div>';
                    data.products.forEach(p => {
                        html += `
                            <a href="${p.url}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-emerald-50 text-gray-700 transition group">
                                <div class="w-6 h-6 rounded bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-gray-900 truncate">${p.title}</div>
                                    <div class="text-[11px] text-gray-500">${p.subtitle}</div>
                                </div>
                                <span class="text-xs font-mono font-bold text-gray-800">${p.price}</span>
                            </a>
                        `;
                    });
                }

                // Suppliers
                if (data.suppliers && data.suppliers.length > 0) {
                    html += '<div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider px-2 pt-2 pb-1">Vendors &amp; Suppliers</div>';
                    data.suppliers.forEach(s => {
                        html += `
                            <a href="${s.url}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-purple-50 text-gray-700 transition group">
                                <div class="w-6 h-6 rounded bg-purple-100 text-purple-700 flex items-center justify-center text-xs">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-gray-900">${s.title}</div>
                                    <div class="text-[11px] text-gray-500">${s.subtitle}</div>
                                </div>
                                <span class="text-xs font-bold text-amber-600">${s.rating} ★</span>
                            </a>
                        `;
                    });
                }

                if (!html) {
                    html = '<div class="text-xs text-gray-400 text-center py-6">No matching products or suppliers found.</div>';
                }

                resultsBox.innerHTML = html;
            } catch(e) {
                console.error("Spotlight search error:", e);
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>