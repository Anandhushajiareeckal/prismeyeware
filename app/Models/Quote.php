<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number', 'customer_id', 'order_id', 'repair_id',
        'quote_date', 'subtotal', 'tax_amount', 'discount_amount',
        'total_amount', 'status', 'notes', 'staff_name'
    ];

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }
}
