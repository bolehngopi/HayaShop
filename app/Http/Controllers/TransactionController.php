<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = Transaction::with('items.product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'total_amount' => 'required|numeric',
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return response()->json(['message' => "Insufficient stock for product {$product->name}"], 400);
            }
        }

        $transaction = Transaction::create([
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'shipping_address' => $validated['shipping_address'],
            'total_amount' => $validated['total_amount'],
        ]);

        foreach ($validated['items'] as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => Product::find($item['product_id'])->price,
            ]);

            Product::find($item['product_id'])->decrement('stock', $item['quantity']);
        }

        // Remove user cart
        $request->user()->carts()->delete();

        return response()->json(['message' => 'Transaction completed', 'transaction' => $transaction], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
