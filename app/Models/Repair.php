<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    protected $fillable = [
        'repair_number', 'customer_id', 'sku', 'repair_date',
        'repair_notes', 'collection_notes', 'assigned_staff', 'repair_price',
        'status', 'completion_date', 'collected_date', 'created_by'
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function items() { return $this->hasMany(RepairItem::class); }
}
