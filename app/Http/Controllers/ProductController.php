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
    public function index()
    {
        $products = Product::paginate(10);

        return response()->json([
            'message' => 'Successfully fetched products',
            'data' => $products
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
                Storage::disk('public')->delete($product->image_cover);
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
            Storage::disk('public')->delete($product->image_cover);
        }

        $product->delete();

        return response()->json([
            'message' => 'Successfully deleted product',
            'data' => $product
        ], 204);
    }
}
