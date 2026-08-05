@extends('layouts.app')

@section('title', 'Product Management')

@section('content')

<div class="flex justify-between items-center mb-6">

    <h1 class="text-3xl font-bold">
        Product Management
    </h1>


    <div class="flex gap-3">

        <!-- Export Excel -->

        <a href="{{ route('products.export') }}"
            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">

            <i class="fas fa-file-excel"></i>

            Export Excel

        </a>


        <!-- Add Product -->

        <a href="{{ route('products.create') }}"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg">

            <i class="fas fa-plus"></i>

            Add Product

        </a>

    </div>

</div>


<!-- Filter Section -->

<div class="bg-white rounded-xl shadow-md p-5 mb-6">

    <form action="{{ route('products.index') }}"
        method="GET">

        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

            <!-- Search -->

            <div>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search Product..."
                    class="w-full border rounded-lg px-4 py-2">

            </div>


            <!-- Category -->

            <div>

                <select
                    name="category"
                    class="w-full border rounded-lg px-4 py-2">

                    <option value="">All Categories</option>

                    @foreach($categories as $category)

                    <option
                        value="{{ $category }}"
                        {{ request('category')==$category ? 'selected':'' }}>

                        {{ $category }}

                    </option>

                    @endforeach

                </select>

            </div>


            <!-- Status -->

            <div>

                <select
                    name="status"
                    class="w-full border rounded-lg px-4 py-2">

                    <option value="">All Status</option>

                    <option
                        value="Active"
                        {{ request('status')=='Active' ? 'selected':'' }}>

                        Active

                    </option>

                    <option
                        value="Inactive"
                        {{ request('status')=='Inactive' ? 'selected':'' }}>

                        Inactive

                    </option>

                </select>

            </div>


            <!-- Min Price -->

            <div>

                <input
                    type="number"
                    name="min_price"
                    value="{{ request('min_price') }}"
                    placeholder="Min Price"
                    class="w-full border rounded-lg px-4 py-2">

            </div>


            <!-- Max Price -->

            <div>

                <input
                    type="number"
                    name="max_price"
                    value="{{ request('max_price') }}"
                    placeholder="Max Price"
                    class="w-full border rounded-lg px-4 py-2">

            </div>


            <!-- Buttons -->

            <div class="flex gap-2">

                <button
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 rounded-lg">

                    <i class="fas fa-search"></i>

                </button>

                <a href="{{ route('products.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg">

                    Reset

                </a>

            </div>

        </div>

    </form>

</div>



<!-- Product Table -->

<div class="bg-white shadow rounded-xl overflow-hidden">

    <table class="min-w-full">

        <thead class="bg-gray-100">

            <tr>

                <th class="px-6 py-3 text-left">Image</th>

                <th class="px-6 py-3 text-left">Name</th>

                <th class="px-6 py-3 text-left">Category</th>

                <th class="px-6 py-3 text-left">Price</th>

                <th class="px-6 py-3 text-left">Quantity</th>

                <th class="px-6 py-3 text-left">Min Stock</th>

                <th class="px-6 py-3 text-left">Stock Status</th>

                <th class="px-6 py-3 text-left">Status</th>

                <th class="px-6 py-3 text-center">Action</th>

            </tr>

        </thead>

        <tbody>

            @forelse($products as $product)

            <tr class="border-b hover:bg-gray-50">

                <td class="px-6 py-4">

                    @if($product->image)

                    <img
                        src="{{ asset('storage/'.$product->image) }}"
                        class="w-16 h-16 rounded object-contain border">

                    @else

                    <div
                        class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">

                        <i class="fas fa-image"></i>

                    </div>

                    @endif

                </td>

                <td class="px-6 py-4">

                    <div class="font-semibold">

                        {{ $product->name }}

                    </div>

                    <div class="text-gray-500 text-sm">

                        {{ Str::limit($product->description,50) }}

                    </div>

                </td>

                <td class="px-6 py-4">

                    <span
                        class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">

                        {{ $product->category }}

                    </span>

                </td>

                <td class="px-6 py-4">

                    ₹{{ number_format($product->price,2) }}

                </td>

                <td class="px-6 py-4">

                    {{ $product->quantity }}

                </td>

                <td class="px-6 py-4">

                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">

                        {{ $product->minimum_stock }}

                    </span>

                </td>

                <td class="px-6 py-4">

                    @if($product->isOutOfStock())

                    <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm">
                        Out of Stock
                    </span>


                    @elseif($product->isLowStock())

                    <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm">
                        Low Stock
                    </span>


                    @else

                    <span class="bg-green-600 text-white px-3 py-1 rounded-full text-sm">
                        Available
                    </span>

                    @endif

                </td>

                <td class="px-6 py-4">

                    @if($product->status=='Active')

                    <span
                        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">

                        Active

                    </span>

                    @else

                    <span
                        class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">

                        Inactive

                    </span>

                    @endif

                </td>

                <td class="px-6 py-4 text-center">

                    <div class="flex justify-center gap-3">

                        <!-- View -->
                        <a href="{{ route('products.show', $product) }}"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded">

                            <i class="fas fa-eye"></i>

                        </a>

                        <!-- Edit -->
                        <a href="{{ route('products.edit', $product) }}"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded">

                            <i class="fas fa-edit"></i>

                        </a>

                        <!-- Delete -->
                        <form action="{{ route('products.destroy', $product) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this product?')">

                            @csrf
                            @method('DELETE')

                            <button
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="9" class="text-center py-10">

                    <i class="fas fa-box-open text-5xl text-gray-400 mb-3"></i>

                    <h2 class="text-xl font-semibold text-gray-600 mt-3">

                        No Products Found

                    </h2>

                    <p class="text-gray-500">

                        Try changing your search or add a new product.

                    </p>

                    <a href="{{ route('products.create') }}"
                        class="inline-block mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg">

                        Add Product

                    </a>

                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>


<!-- Pagination -->

@if($products->hasPages())

<div class="mt-6 flex justify-center">

    {{ $products->onEachSide(1)->links() }}

</div>

@endif

@endsection