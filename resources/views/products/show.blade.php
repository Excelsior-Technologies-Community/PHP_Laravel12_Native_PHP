@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
        </div>

        <div class="card overflow-hidden">
            <div class="md:flex">
                <!-- Product Image -->
                <div class="md:w-2/5 bg-gray-100 p-8 flex items-center justify-center">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}"
                             class="w-full h-auto max-h-96 object-cover rounded-lg">
                    @else
                        <div class="text-center">
                            <i class="fas fa-box text-8xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">No image available</p>
                        </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="md:w-3/5 p-8">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full 
                                       {{ $product->quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                            </span>
                            <h1 class="text-3xl font-bold text-gray-800 mt-2">{{ $product->name }}</h1>
                        </div>
                        <div class="text-3xl font-bold text-indigo-600">
                            ${{ number_format($product->price, 2) }}
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Description</h3>
                        <div class="prose max-w-none">
                            {{ $product->description ? nl2br(e($product->description)) : 'No description available.' }}
                        </div>
                    </div>

                    <!-- Product Info Grid -->
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Quantity Available</h4>
                            <p class="text-2xl font-bold text-gray-800">{{ $product->quantity }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-1">Created Date</h4>
                            <p class="text-lg text-gray-800">{{ $product->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('products.edit', $product) }}" 
                           class="flex-1 bg-yellow-50 text-yellow-700 border border-yellow-200 py-3 rounded-lg font-medium text-center hover:bg-yellow-100 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit Product
                        </a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to delete this product?')"
                                    class="w-full bg-red-50 text-red-700 border border-red-200 py-3 rounded-lg font-medium hover:bg-red-100 transition-colors">
                                <i class="fas fa-trash mr-2"></i>Delete Product
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="card p-6 text-center">
                <div class="text-4xl text-indigo-600 mb-3">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="font-semibold text-gray-700 mb-1">Created</h3>
                <p class="text-gray-600">{{ $product->created_at->diffForHumans() }}</p>
            </div>
            
            <div class="card p-6 text-center">
                <div class="text-4xl text-green-600 mb-3">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <h3 class="font-semibold text-gray-700 mb-1">Last Updated</h3>
                <p class="text-gray-600">{{ $product->updated_at->diffForHumans() }}</p>
            </div>
            
            <div class="card p-6 text-center">
                <div class="text-4xl text-purple-600 mb-3">
                    <i class="fas fa-hashtag"></i>
                </div>
                <h3 class="font-semibold text-gray-700 mb-1">Product ID</h3>
                <p class="text-gray-600 font-mono">{{ $product->id }}</p>
            </div>
        </div>
    </div>
@endsection