<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Fetch cart items with product details
            $cartItems = Cart::with('product')
                ->where('user_id', Auth::id())
                ->get();

            // Handle case where the cart is empty
            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'Your cart is empty'], 200);
            }

            // Return the cart items in a structured response
            return response()->json([
                'success' => true,
                'cart' => $cartItems,
                'total_items' => $cartItems->count(),
                'total_price' => $cartItems->sum(fn($item) => $item->product->price * $item->quantity),
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions and return a generic error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the cart',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock < $validated['quantity']) {
            return response()->json(['message' => 'Insufficient stock'], 400);
        }

        $cartItem = Cart::updateOrCreate(
            ['user_id' => $request->user()->id, 'product_id' => $validated['product_id']],
            ['quantity' => $validated['quantity']]
        );

        return response()->json(['message' => 'Item added to cart', 'cart' => $cartItem]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}
