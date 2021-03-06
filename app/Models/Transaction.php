<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'updated_by', 'status', 'quantity', 'amount', 'active'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function scopeNonCompletedTransactions($query)
    {
        return $query->whereNotIn('status', [0, 1])->where('active', 1);
    }
}
