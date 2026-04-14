<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitHistory extends Model
{
    protected $fillable = ['customer_id', 'visit_date', 'visit_type', 'staff_attended', 'notes', 'outcome'];

    public function customer() { return $this->belongsTo(Customer::class); }
}
