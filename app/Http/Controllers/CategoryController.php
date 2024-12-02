<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('products')->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'image_cover' => 'nullable|image',
        ]);

        // Handle image upload
        $imagePath = $request->file('image_cover') ? asset($request
            ->file('image_cover')->store('categories', 'public')) : null;

        $category = Category::create(array_merge($request->all(), ['image_cover' => $imagePath ?? null]));

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json([
            'message' => 'Successfully fetched category',
            'data' => $category,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'string',
            'description' => 'string',
            'image_cover' => 'image',
        ]);

        // Handle image replacement if a new one is uploaded
        if ($request->hasFile('image_cover')) {
            if ($category->image_cover && file_exists(storage_path($category->image_cover))) {
                Storage::disk('public')->delete(storage_path($category->image_cover));
            }

            $image = $request->file('image_cover')->store('categories');
            $category->image_cover = asset($image);
        }

        $category->update($request->all());

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
