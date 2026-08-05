@extends('layouts.app')

@section('title', 'Home')

@section('content')

<!-- Hero -->

<div
    class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-10 text-white shadow-lg mb-10">

    <div class="flex flex-col lg:flex-row justify-between items-center">

        <div>

            <h1 class="text-5xl font-bold mb-4">

                Welcome to ProductHub

            </h1>

            <p class="text-lg opacity-90">

                Manage products quickly and efficiently with Laravel.

            </p>

            <div class="mt-8 flex gap-4">

                <a
                    href="{{ route('products.index') }}"
                    class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100">

                    View Products

                </a>

                <a
                    href="{{ route('products.create') }}"
                    class="border border-white px-6 py-3 rounded-lg hover:bg-white hover:text-indigo-600">

                    Add Product

                </a>

            </div>

        </div>

        <div class="hidden lg:block">

            <i class="fas fa-box-open text-9xl opacity-30"></i>

        </div>

    </div>

</div>



<!-- Dashboard Statistics -->

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">


    <!-- Total Products -->

    <div class="bg-white rounded-xl shadow p-6">

        <div class="flex justify-between items-center">

            <div>

                <p class="text-gray-500">

                    Total Products

                </p>

                <h2 class="text-3xl font-bold mt-2">

                    {{ $totalProducts }}

                </h2>

            </div>

            <div class="bg-indigo-100 p-4 rounded-full">

                <i class="fas fa-box text-indigo-600 text-2xl"></i>

            </div>

        </div>

    </div>



    <!-- Active -->

    <div class="bg-white rounded-xl shadow p-6">

        <div class="flex justify-between items-center">

            <div>

                <p class="text-gray-500">

                    Active

                </p>

                <h2 class="text-3xl font-bold text-green-600 mt-2">

                    {{ $activeProducts }}

                </h2>

            </div>

            <div class="bg-green-100 p-4 rounded-full">

                <i class="fas fa-check-circle text-green-600 text-2xl"></i>

            </div>

        </div>

    </div>



    <!-- Inactive -->

    <div class="bg-white rounded-xl shadow p-6">

        <div class="flex justify-between items-center">

            <div>

                <p class="text-gray-500">

                    Inactive

                </p>

                <h2 class="text-3xl font-bold text-red-600 mt-2">

                    {{ $inactiveProducts }}

                </h2>

            </div>

            <div class="bg-red-100 p-4 rounded-full">

                <i class="fas fa-times-circle text-red-600 text-2xl"></i>

            </div>

        </div>

    </div>



    <!-- Categories -->

    <div class="bg-white rounded-xl shadow p-6">

        <div class="flex justify-between items-center">

            <div>

                <p class="text-gray-500">

                    Categories

                </p>

                <h2 class="text-3xl font-bold text-blue-600 mt-2">

                    {{ $categories }}

                </h2>

            </div>

            <div class="bg-blue-100 p-4 rounded-full">

                <i class="fas fa-tags text-blue-600 text-2xl"></i>

            </div>

        </div>

    </div>



    <!-- Stock -->

    <div class="bg-white rounded-xl shadow p-6">

        <div class="flex justify-between items-center">

            <div>

                <p class="text-gray-500">

                    Total Stock

                </p>

                <h2 class="text-3xl font-bold text-purple-600 mt-2">

                    {{ $totalStock }}

                </h2>

            </div>

            <div class="bg-purple-100 p-4 rounded-full">

                <i class="fas fa-warehouse text-purple-600 text-2xl"></i>

            </div>

        </div>

    </div>


    <!-- Low Stock Products -->

    <div class="bg-white rounded-xl shadow p-6">

        <div class="flex justify-between items-center">

            <div>

                <p class="text-gray-500">
                    Low Stock
                </p>

                <h2 class="text-3xl font-bold text-yellow-600 mt-2">
                    {{ $lowStockProducts }}
                </h2>

            </div>

            <div class="bg-yellow-100 p-4 rounded-full">

                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>

            </div>

        </div>

    </div>



    <!-- Out Of Stock Products -->

    <div class="bg-white rounded-xl shadow p-6">

        <div class="flex justify-between items-center">

            <div>

                <p class="text-gray-500">
                    Out Of Stock
                </p>

                <h2 class="text-3xl font-bold text-red-600 mt-2">
                    {{ $outOfStockProducts }}
                </h2>

            </div>

            <div class="bg-red-100 p-4 rounded-full">

                <i class="fas fa-times-circle text-red-600 text-2xl"></i>

            </div>

        </div>

    </div>

</div>

    <!-- Latest Products -->

    <div class="flex justify-between items-center mb-6">

        <h2 class="text-3xl font-bold">

            Latest Products

        </h2>

        <a
            href="{{ route('products.index') }}"
            class="text-indigo-600 hover:text-indigo-800 font-semibold">

            View All →

        </a>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @if($products->count())

        @foreach($products as $product)

        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition">

            <!-- Product Image -->
            <div class="h-56 bg-gray-100 flex items-center justify-center">

                @if($product->image)

                <img
                    src="{{ asset('storage/'.$product->image) }}"
                    alt="{{ $product->name }}"
                     class="w-full h-full object-contain p-3">

                @else

                <i class="fas fa-box text-6xl text-gray-400"></i>

                @endif

            </div>

            <div class="p-5">

                <!-- Name -->
                <h3 class="text-xl font-bold mb-2">

                    {{ $product->name }}

                </h3>

                <!-- Description -->
                <p class="text-gray-600 text-sm mb-4">

                    {{ Str::limit($product->description, 80) }}

                </p>

                <!-- Category & Status -->
                <div class="flex justify-between items-center mb-4">

                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">

                        {{ $product->category }}

                    </span>

                    @if($product->status == 'Active')

                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">

                        Active

                    </span>

                    @else

                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">

                        Inactive

                    </span>

                    @endif

                </div>

                <!-- Price & Quantity -->
                <div class="flex justify-between items-center mb-5">

                    <div>

                        <p class="text-2xl font-bold text-indigo-600">

                            ₹{{ number_format($product->price,2) }}

                        </p>

                    </div>

                    <span class="bg-gray-100 px-3 py-1 rounded-lg text-sm">

                        Stock :
                        {{ $product->quantity }}

                    </span>

                </div>

                <!-- Buttons -->
                <div class="flex gap-3">

                    <a
                        href="{{ route('products.show',$product) }}"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-center">

                        <i class="fas fa-eye mr-1"></i>

                        View

                    </a>

                    <a
                        href="{{ route('products.edit',$product) }}"
                        class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg text-center">

                        <i class="fas fa-edit mr-1"></i>

                        Edit

                    </a>

                </div>

            </div>

        </div>

        @endforeach

        @else

        <div class="col-span-3">

            <div class="bg-white rounded-xl shadow p-12 text-center">

                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>

                <h3 class="text-2xl font-bold text-gray-600">

                    No Products Found

                </h3>

                <p class="text-gray-500 mt-2">

                    Start by adding your first product.

                </p>

                <a
                    href="{{ route('products.create') }}"
                    class="inline-block mt-6 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg">

                    <i class="fas fa-plus mr-2"></i>

                    Add Product

                </a>

            </div>

        </div>

        @endif

    </div>

    @endsection