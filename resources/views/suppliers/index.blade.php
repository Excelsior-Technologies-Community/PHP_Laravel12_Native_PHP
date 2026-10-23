@extends('layouts.app')

@section('title', 'Suppliers Directory')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">
                    Procurement &amp; Vendors
                </span>
                <span class="text-xs text-white/80">Direct Vendor Relationships</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fas fa-truck text-indigo-300"></i> Supplier &amp; Vendor Directory
            </h1>
            <p class="text-white/80 text-sm mt-1">
                Manage vendor profiles, purchase order terms, lead times, and reliability scorecard ratings.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('suppliers.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2 text-sm transition">
                <i class="fas fa-plus"></i> Onboard New Supplier
            </a>
            <a href="{{ route('inventory.watchdog') }}" class="bg-white text-indigo-700 hover:bg-gray-100 font-bold px-4 py-2.5 rounded-lg shadow-md flex items-center gap-2 text-sm transition">
                <i class="fas fa-triangle-exclamation"></i> Low-Stock Watchdog
            </a>
        </div>
    </div>

    {{-- Scorecard Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-indigo-600">
            <span class="text-xs font-bold uppercase text-gray-500">Active Supply Partners</span>
            <div class="text-2xl font-black text-indigo-700 mt-0.5">{{ $totalSuppliers }} Vendors</div>
            <span class="text-[11px] text-gray-400">Verified tier-1 suppliers</span>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-emerald-500">
            <span class="text-xs font-bold uppercase text-gray-500">Average Fulfillment Lead Time</span>
            <div class="text-2xl font-black text-emerald-600 mt-0.5">{{ $avgLeadTime }} Days</div>
            <span class="text-[11px] text-gray-400">From order placement to dock delivery</span>
        </div>
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-amber-500">
            <span class="text-xs font-bold uppercase text-gray-500">High Reliability (4.5★+)</span>
            <div class="text-2xl font-black text-amber-600 mt-0.5">{{ $topRatedCount }} Suppliers</div>
            <span class="text-[11px] text-gray-400">Top-rated on-time delivery</span>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form action="{{ route('suppliers.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search supplier by name, contact person, email or phone..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-lg text-sm">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('suppliers.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-lg text-sm flex items-center">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Suppliers Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        @forelse($suppliers as $s)
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition flex flex-col justify-between">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h2 class="font-bold text-gray-900 text-lg leading-snug">
                                <a href="{{ route('suppliers.show', $s->id) }}" class="hover:text-indigo-600">
                                    {{ $s->name }}
                                </a>
                            </h2>
                            <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                <i class="fas fa-user text-gray-400"></i> {{ $s->contact_person ?? 'Account Mgr' }}
                            </div>
                        </div>

                        {{-- Rating Badge --}}
                        <span class="bg-amber-50 text-amber-700 font-black text-xs px-2.5 py-1 rounded-full border border-amber-200 flex items-center gap-1 shadow-2xs">
                            <i class="fas fa-star text-amber-500 text-[10px]"></i> {{ number_format($s->reliability_score, 1) }}
                        </span>
                    </div>

                    <div class="space-y-2 text-xs text-gray-600 pt-2 border-t border-gray-100">
                        @if($s->email)
                            <div class="flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-400 w-4 text-center"></i>
                                <a href="mailto:{{ $s->email }}" class="text-indigo-600 hover:underline truncate">{{ $s->email }}</a>
                            </div>
                        @endif
                        @if($s->phone)
                            <div class="flex items-center gap-2">
                                <i class="fas fa-phone text-gray-400 w-4 text-center"></i>
                                <span>{{ $s->phone }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between pt-1">
                            <span>Payment Terms: <strong>{{ $s->payment_terms }}</strong></span>
                            <span>Lead Time: <strong class="text-emerald-700">{{ $s->lead_time_days }} days</strong></span>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex justify-between items-center text-xs">
                    <span class="font-bold text-gray-700">
                        <i class="fas fa-boxes text-indigo-500 mr-1"></i> {{ $s->products_count }} Products
                    </span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('suppliers.show', $s->id) }}" class="text-indigo-600 hover:text-indigo-800 font-bold">
                            View Products &rarr;
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-8 rounded-xl text-center text-gray-500">
                <i class="fas fa-truck text-4xl mb-2 text-gray-300"></i>
                <p>No suppliers found matching your query.</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $suppliers->links() }}
    </div>

</div>
@endsection
