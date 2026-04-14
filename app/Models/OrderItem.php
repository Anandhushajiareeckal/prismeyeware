<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_name', 'sku', 'category',
        'quantity', 'unit_price', 'discount', 'tax', 'total'
    ];

    public function order() { return $this->belongsTo(Order::class); }
}
