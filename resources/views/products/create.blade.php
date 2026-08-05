@extends('layouts.app')

@section('title', isset($product) ? 'Edit Product' : 'Create Product')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="bg-white shadow-lg rounded-xl p-8">

        <h2 class="text-3xl font-bold text-gray-800 mb-8">

            {{ isset($product) ? 'Edit Product' : 'Add New Product' }}

        </h2>

        <form
            action="{{ isset($product) ? route('products.update',$product) : route('products.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            @if(isset($product))
            @method('PUT')
            @endif


            <!-- Product Name -->

            <div class="mb-6">

                <label class="block font-semibold mb-2">

                    Product Name

                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name',$product->name ?? '') }}"
                    class="w-full border rounded-lg px-4 py-3"
                    placeholder="Enter Product Name">

                @error('name')

                <p class="text-red-600 mt-1">

                    {{ $message }}

                </p>

                @enderror

            </div>



            <!-- Category -->

            <div class="mb-6">

                <label class="block font-semibold mb-2">

                    Category

                </label>

                <select
                    name="category"
                    class="w-full border rounded-lg px-4 py-3">

                    <option value="">Select Category</option>

                    <option value="Electronics"
                        {{ old('category',$product->category ?? '')=='Electronics' ? 'selected' : '' }}>

                        Electronics

                    </option>

                    <option value="Fashion"
                        {{ old('category',$product->category ?? '')=='Fashion' ? 'selected' : '' }}>

                        Fashion

                    </option>

                    <option value="Furniture"
                        {{ old('category',$product->category ?? '')=='Furniture' ? 'selected' : '' }}>

                        Furniture

                    </option>

                    <option value="Books"
                        {{ old('category',$product->category ?? '')=='Books' ? 'selected' : '' }}>

                        Books

                    </option>

                    <option value="Sports"
                        {{ old('category',$product->category ?? '')=='Sports' ? 'selected' : '' }}>

                        Sports

                    </option>

                    <option value="Accessories"
                        {{ old('category',$product->category ?? '')=='Accessories' ? 'selected' : '' }}>

                        Accessories

                    </option>

                </select>

                @error('category')

                <p class="text-red-600 mt-1">

                    {{ $message }}

                </p>

                @enderror

            </div>



            <!-- Description -->

            <div class="mb-6">

                <label class="block font-semibold mb-2">

                    Description

                </label>

                <textarea
                    name="description"
                    rows="5"
                    class="w-full border rounded-lg px-4 py-3"
                    placeholder="Product Description">{{ old('description',$product->description ?? '') }}</textarea>

                @error('description')

                <p class="text-red-600 mt-1">

                    {{ $message }}

                </p>

                @enderror

            </div>



            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Price -->

                <div>

                    <label class="block font-semibold mb-2">

                        Price

                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="price"
                        value="{{ old('price',$product->price ?? '') }}"
                        class="w-full border rounded-lg px-4 py-3"
                        placeholder="Enter Price">

                    @error('price')

                    <p class="text-red-600 mt-1">

                        {{ $message }}

                    </p>

                    @enderror

                </div>



                <!-- Quantity -->

                <div>

                    <label class="block font-semibold mb-2">

                        Quantity

                    </label>

                    <input
                        type="number"
                        name="quantity"
                        value="{{ old('quantity',$product->quantity ?? '') }}"
                        class="w-full border rounded-lg px-4 py-3"
                        placeholder="Enter Quantity">

                    @error('quantity')

                    <p class="text-red-600 mt-1">

                        {{ $message }}

                    </p>

                    @enderror

                </div>

            </div>



            <!-- Status -->

            <div class="mt-6">

                <label class="block font-semibold mb-2">

                    Status

                </label>

                <select
                    name="status"
                    class="w-full border rounded-lg px-4 py-3">

                    <option value="Active"
                        {{ old('status',$product->status ?? 'Active')=='Active' ? 'selected' : '' }}>

                        Active

                    </option>

                    <option value="Inactive"
                        {{ old('status',$product->status ?? '')=='Inactive' ? 'selected' : '' }}>

                        Inactive

                    </option>

                </select>

                @error('status')

                <p class="text-red-600 mt-1">

                    {{ $message }}

                </p>

                @enderror

            </div>

            <!-- Product Image -->

            <div class="mt-6">

                <label class="block font-semibold mb-2">

                    Product Image

                </label>

                <input
                    type="file"
                    name="image"
                    id="image"
                    accept="image/*"
                    onchange="previewImage(event)"
                    class="w-full border rounded-lg px-4 py-3">

                @error('image')

                <p class="text-red-600 mt-1">

                    {{ $message }}

                </p>

                @enderror

            </div>


            <!-- Existing Image -->

            @if(isset($product) && $product->image)

            <div class="mt-6">

                <label class="block font-semibold mb-2">

                    Current Image

                </label>

                <img
                    src="{{ asset('storage/'.$product->image) }}"
                    class="w-40 h-40 rounded-lg object-cover border">

            </div>

            @endif


            <!-- Preview Image -->

            <div id="preview-container"
                class="mt-6 hidden">

                <label class="block font-semibold mb-2">

                    Preview

                </label>

                <img
                    id="preview"
                    class="w-40 h-40 rounded-lg border object-cover">

            </div>


            <!-- Buttons -->

            <div class="flex justify-end gap-4 mt-8">

                <a href="{{ route('products.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg">

                    Cancel

                </a>

                <button
                    type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg">

                    @if(isset($product))

                    Update Product

                    @else

                    Save Product

                    @endif

                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>
    function previewImage(event) {

        const file = event.target.files[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            document
                .getElementById('preview')
                .src = e.target.result;

            document
                .getElementById('preview-container')
                .classList.remove('hidden');
        }

        reader.readAsDataURL(file);

    }
</script>

@endpush