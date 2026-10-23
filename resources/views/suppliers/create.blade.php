@extends('layouts.app')

@section('title', 'Onboard New Supplier')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fas fa-truck text-indigo-300"></i> Onboard New Supplier
            </h1>
            <p class="text-white/80 text-sm mt-1">Register a vendor partner with lead times, contacts, and terms.</p>
        </div>
        <a href="{{ route('suppliers.index') }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
            &larr; Back to Suppliers
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-100">
        <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Company / Supplier Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Apex Tech Distributors Inc." class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" placeholder="e.g. Elena Rostova" class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="procurement@company.com" class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000" class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Payment Terms *</label>
                    <select name="payment_terms" required class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="Net 15" {{ old('payment_terms') == 'Net 15' ? 'selected' : '' }}>Net 15</option>
                        <option value="Net 30" {{ old('payment_terms', 'Net 30') == 'Net 30' ? 'selected' : '' }}>Net 30</option>
                        <option value="Net 45" {{ old('payment_terms') == 'Net 45' ? 'selected' : '' }}>Net 45</option>
                        <option value="Net 60" {{ old('payment_terms') == 'Net 60' ? 'selected' : '' }}>Net 60</option>
                        <option value="Due on Receipt" {{ old('payment_terms') == 'Due on Receipt' ? 'selected' : '' }}>Due on Receipt</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Delivery Lead Time (Days) *</label>
                    <input type="number" name="lead_time_days" value="{{ old('lead_time_days', 7) }}" min="1" max="365" required class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Initial Reliability Score (1.0 - 5.0) *</label>
                    <input type="number" step="0.1" name="reliability_score" value="{{ old('reliability_score', 4.8) }}" min="1" max="5" required class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Physical / Warehouse Address</label>
                    <textarea name="address" rows="2" placeholder="Street, City, State, ZIP..." class="w-full border rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Notes / Supply Categories</label>
                    <textarea name="notes" rows="2" placeholder="Any internal notes or specialties..." class="w-full border rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-3 border-t">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg text-sm shadow">
                    Save &amp; Onboard Supplier
                </button>
                <a href="{{ route('suppliers.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-5 rounded-lg text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
