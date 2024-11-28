@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Products</h1>
        <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add
            Product</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($products as $product)
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="font-bold text-lg">{{ $product->name }}</h2>
                <p class="text-sm text-gray-600">{{ $product->description }}</p>
                <p class="text-blue-600 font-bold mt-2">${{ $product->price }}</p>
                <div class="flex justify-between items-center mt-4">
                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600 hover:underline">Edit</a>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline"
                            onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">
        {{ $products->links() }} <!-- Pagination -->
    </div>
@endsection
