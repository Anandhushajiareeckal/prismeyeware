<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'order_date', 'total_amount',
        'tax_amount', 'discount_amount', 'order_status', 'sales_staff'
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
}
