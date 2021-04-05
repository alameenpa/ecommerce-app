<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['address', 'created_by', 'amount', 'status'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'order_id', 'id');
    }

    public function scopeNonCompletedTransactions($query)
    {
        return $query->whereNotIn('status', [0, 4]);
    }
}
