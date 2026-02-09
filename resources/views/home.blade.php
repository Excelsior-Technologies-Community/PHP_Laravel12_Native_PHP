@extends('layouts.app')

@section('title', 'Home - ProductHub')

@section('content')
    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl font-bold mb-6">Welcome to ProductHub</h1>
            <p class="text-xl mb-8 opacity-90">A simple yet powerful product management system built with Laravel</p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('products.index') }}" class="btn-primary text-lg px-8 py-3">
                    <i class="fas fa-boxes mr-2"></i>View Products
                </a>
                <a href="{{ route('products.create') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold text-lg border-2 border-indigo-600 hover:bg-indigo-50 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
        <div class="card p-6">
            <div class="text-indigo-600 text-4xl mb-4">
                <i class="fas fa-bolt"></i>
            </div>
            <h3 class="text-xl font-bold mb-3">Fast & Efficient</h3>
            <p class="text-gray-600">Built with Laravel's powerful features for optimal performance.</p>
        </div>
        
        <div class="card p-6">
            <div class="text-green-600 text-4xl mb-4">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 class="text-xl font-bold mb-3">Secure</h3>
            <p class="text-gray-600">Enterprise-level security with built-in protection.</p>
        </div>
        
        <div class="card p-6">
            <div class="text-yellow-600 text-4xl mb-4">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <h3 class="text-xl font-bold mb-3">Responsive</h3>
            <p class="text-gray-600">Works perfectly on all devices and screen sizes.</p>
        </div>
    </div>

    <!-- Latest Products -->
    <div class="mt-12">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Latest Products</h2>
            <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                View all <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="card p-5">
                        <div class="mb-4">
                            <div class="bg-gray-100 h-48 rounded-lg flex items-center justify-center">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover rounded-lg">
                                @else
                                    <i class="fas fa-box text-6xl text-gray-400"></i>
                                @endif
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($product->description, 100) }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-indigo-600">${{ number_format($product->price, 2) }}</span>
                            <span class="bg-gray-100 px-3 py-1 rounded-full text-sm">
                                {{ $product->quantity }} in stock
                            </span>
                        </div>
                        <div class="mt-6 flex space-x-3">
                            <a href="{{ route('products.show', $product) }}" class="flex-1 bg-indigo-50 text-indigo-600 text-center py-2 rounded-lg font-medium hover:bg-indigo-100 transition-colors">
                                <i class="fas fa-eye mr-2"></i>View
                            </a>
                            <a href="{{ route('products.edit', $product) }}" class="flex-1 bg-yellow-50 text-yellow-600 text-center py-2 rounded-lg font-medium hover:bg-yellow-100 transition-colors">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No products yet</h3>
                <p class="text-gray-500 mb-6">Start by adding your first product</p>
                <a href="{{ route('products.create') }}" class="btn-primary inline-block">
                    <i class="fas fa-plus mr-2"></i>Add First Product
                </a>
            </div>
        @endif
    </div>
@endsection