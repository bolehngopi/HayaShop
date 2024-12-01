<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $request->validate([
    //         'sort' => 'string|in:price,name,stock',
    //         'order' => 'string|in:asc,desc',
    //         'per_page' => 'integer',
    //         'page' => 'integer',
    //         'category' => 'integer|exists:category,id'
    //     ]);

    //     $products = Product::where('category_id', 'like', '%' . $request->category . '%')
    //         ->where('price', '>=', $request->price ?? 0)
    //         ->where('price', '<=', $request->priceMax ?? PHP_INT_MAX)
    //         ->where('stock', '>', $request->availability === 'in_stock' ? 0 : -1)
    //         ->orderBy($request->sort ?? 'created_at', $request->order ?? 'asc')
    //         ->paginate($request->per_page ?? 10);
    //     // paginate($request->per_page ?? 10, ['*'], 'page', $request->page ?? 1);

    //     // if ($request->has('category')) {
    //     //     $products = $products->filter(function ($product) use ($request) {
    //     //         return $product->category_id == $request->category;
    //     //     });
    //     // }

    //     // Handle filter by price
    //     // if ($request->has('price')) {
    //     //     $products = $products->filter(function ($product) use ($request) {
    //     //         return $product->price >= $request->price;
    //     //     });
    //     // }

    //     // if ($request->has('priceMin') || $request->has('priceMax')) {
    //     //     $products = $products->filter(function ($product) use ($request) {
    //     //         $priceMin = $request->priceMin ?? 0;
    //     //         $priceMax = $request->priceMax ?? PHP_INT_MAX;
    //     //         return $product->price >= $priceMin && $product->price <= $priceMax;
    //     //     });
    //     // }

    //     // if ($request->has('availability')) {
    //     //     $products = $products->filter(function ($product) use ($request) {
    //     //         return $request->availability === 'in_stock' ? $product->stock > 0 : $product->stock === 0;
    //     //     });
    //     // }

    //     return response()->json([
    //         'message' => 'Successfully fetched products',
    //         'data' => $products
    //     ]);
    // }

    public function index(Request $request)
    {
        $request->validate([
            'sort' => 'string|in:price,name,stock',
            'order' => 'string|in:asc,desc',
            'per_page' => 'integer',
            'page' => 'integer',
            'category' => 'integer|exists:categories,id', // Adjust the table name if necessary
            'priceMin' => 'numeric',
            'priceMax' => 'numeric',
            'availability' => 'string|in:in_stock,out_of_stock',
        ]);

        $query = Product::query();

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Apply price filters
        if ($request->filled('priceMin')) {
            $query->where('price', '>=', $request->priceMin);
        }
        if ($request->filled('priceMax')) {
            $query->where('price', '<=', $request->priceMax);
        }

        // Apply availability filter
        if ($request->filled('availability')) {
            if ($request->availability === 'in_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->availability === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            }
        }

        // Apply sorting
        if ($request->filled('sort') && $request->filled('order')) {
            $query->orderBy($request->sort, $request->order);
        }

        // Paginate the results
        $products = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'message' => 'Successfully fetched products',
            'data' => $products,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'integer|exists:category,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image_cover' => 'image',
        ]);

        // Handle image upload
        $imagePath = $request->file('image_cover') ? asset($request->file('image_cover')->store('products', 'public')) : null;

        $product = Product::create(array_merge($validated, ['image_cover' => $imagePath]));

        return response()->json([
            'message' => 'Successfully created product',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'message' => 'Successfully fetched product',
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'integer|exists:category,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image_cover' => 'image',
        ]);

        // Handle image replacement if a new one is uploaded
        if ($request->hasFile('image_cover')) {
            if ($product->image_cover) {
                Storage::disk('public')->delete(storage_path($product->image_cover));
            }

            $image = $request->file('image_cover')->store('products');
            $product->image_cover = asset($image);
        }

        $product->update(array_merge($request->except('image_cover'), ['image_cover' => $image ?? '']));

        return response()->json([
            'message' => 'Successfully updated product',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image_cover) {
            Storage::disk('public')->delete(storage_path($product->image_cover));
        }

        $product->delete();

        return response()->json([
            'message' => 'Successfully deleted product',
            'data' => $product
        ], 204);
    }

    /**
     *  Search for a product
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string'
        ]);

        $products = Product::where('name', 'like', "%" . $request->query . "%")->get();

        // Filter the category of the product
        if ($request->has('category')) {
            $products = $products->filter(function ($product) use ($request) {
                return $product->category_id == $request->category;
            });
        }

        return response()->json([
            'message' => 'Successfully fetched products',
            'data' => $products
        ]);
    }
}
