@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-extrabold text-white flex items-center gap-2">
                <i class="fas fa-edit text-indigo-300"></i> Edit Supplier: {{ $supplier->name }}
            </h1>
            <p class="text-white/80 text-sm mt-1">Update vendor terms, contact information, and reliability metrics.</p>
        </div>
        <a href="{{ route('suppliers.show', $supplier->id) }}" class="bg-white/20 hover:bg-white/30 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
            &larr; Back to Profile
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-100">
        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Company / Supplier Name *</label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Payment Terms *</label>
                    <select name="payment_terms" required class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach(['Net 15', 'Net 30', 'Net 45', 'Net 60', 'Due on Receipt'] as $term)
                            <option value="{{ $term }}" {{ old('payment_terms', $supplier->payment_terms) == $term ? 'selected' : '' }}>{{ $term }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Delivery Lead Time (Days) *</label>
                    <input type="number" name="lead_time_days" value="{{ old('lead_time_days', $supplier->lead_time_days) }}" min="1" max="365" required class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Reliability Score (1.0 - 5.0) *</label>
                    <input type="number" step="0.1" name="reliability_score" value="{{ old('reliability_score', $supplier->reliability_score) }}" min="1" max="5" required class="w-full border rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Physical / Warehouse Address</label>
                    <textarea name="address" rows="2" class="w-full border rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('address', $supplier->address) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Notes / Specialties</label>
                    <textarea name="notes" rows="2" class="w-full border rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('notes', $supplier->notes) }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-3 border-t">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg text-sm shadow">
                    Update Supplier Profile
                </button>
                <a href="{{ route('suppliers.show', $supplier->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-5 rounded-lg text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
