<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'sort' => 'string|in:price,name,stock',
            'order' => 'string|in:asc,desc',
            'per_page' => 'integer|min:1',
            'page' => 'integer|min:1',
            'category' => 'integer|exists:categories,id',
            'priceMin' => 'numeric|min:0',
            'priceMax' => 'numeric|min:0',
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
            $query->where('stock', $request->availability === 'in_stock' ? '>' : '<=', 0);
        }

        // Apply sorting
        if ($request->filled('sort') && $request->filled('order')) {
            $query->orderBy($request->sort, $request->order);
        }

        // Paginate results
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
            'category_id' => 'integer|exists:categories,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_cover' => 'nullable|image',
        ]);

        // Handle image upload
        $imagePath = $request->file('image_cover')
            ? $request->file('image_cover')->store('products', 'public')
            : null;

        $product = Product::create(array_merge($validated, [
            'image_cover' => $imagePath ? asset("storage/$imagePath") : null,
        ]));

        return response()->json([
            'message' => 'Successfully created product',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'message' => 'Successfully fetched product',
            'data' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'integer|exists:categories,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_cover' => 'nullable|image|max:2048',
        ]);

        Log::info(request()->all());
        Log::info($request->all());

        // Handle image upload if present
        if ($request->hasFile('image_cover')) {
            $imagePath = $request->file('image_cover')->store('products', 'public');
            $validated['image_cover'] = asset("storage/$imagePath");

            // Optional: delete old image if necessary
            if ($product->image_cover) {
                Storage::delete(str_replace(asset('storage/'), '', $product->image_cover));
            }
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Successfully updated product',
            'data' => $product,
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
        ], 200);
    }

    /**
     * Search for a product.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
            'category' => 'integer|exists:categories,id',
        ]);

        $query = Product::query();

        // Filter by search term
        if ($request->filled('query')) {
            $query->where('name', 'like', '%' . $request->input('query') . '%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->get();

        return response()->json([
            'message' => $products->isEmpty() ? 'No products found' : 'Successfully fetched products',
            'results' => $products,
        ]);
    }
}
