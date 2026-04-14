<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'order_id', 'repair_id',
        'invoice_date', 'subtotal', 'tax_amount', 'discount_amount',
        'total_amount', 'payment_mode', 'payment_status', 'notes', 'staff_name'
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function order() { return $this->belongsTo(Order::class); }
    public function repair() { return $this->belongsTo(Repair::class); }
    public function items() { return $this->hasMany(InvoiceItem::class); }
}
