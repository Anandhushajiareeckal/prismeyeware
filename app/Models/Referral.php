<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = ['customer_id', 'referred_by', 'referral_date', 'notes'];

    public function customer() { return $this->belongsTo(Customer::class); }
}
