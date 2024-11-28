<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('items.product')->where('user_id', auth()->id())->get();
        return view('transactions.index', compact('transactions'));
    }

    public function checkout(Request $request)
    {
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['error' => 'Cart is empty']);
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $product = $item->product;
            if ($product->stock < $item->quantity) {
                return redirect()->route('cart.index')->withErrors(['error' => "Insufficient stock for product: {$product->name}"]);
            }
            $total += $product->price * $item->quantity;
        }

        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'total_amount' => $total,
            'status' => 'pending',
        ]);

        foreach ($cartItems as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);

            $item->product->decrement('stock', $item->quantity);
            $item->delete();
        }

        return redirect()->route('transactions.index')->with('success', 'Checkout successful');
    }
}
