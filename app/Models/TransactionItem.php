<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the product that owns the transaction item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the transaction that owns the transaction item.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
