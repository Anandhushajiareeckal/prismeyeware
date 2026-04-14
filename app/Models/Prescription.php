<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'customer_id', 'prescription_date', 'recall_date', 'type', 'doctor_name',
        'eye_side', 'sphere', 'cylinder', 'axis', 'h_prism', 'v_prism',
        'add', 'intermediate_add', 'comments', 'created_by'
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
