<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_number', 'first_name', 'last_name', 'gender', 'date_of_birth',
        'phone_number', 'alternate_phone_number', 'email', 'address_line_1',
        'address_line_2', 'city', 'state', 'country', 'postal_code',
        'preferred_store', 'status', 'referred_by', 'created_by', 'updated_by'
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute() {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function notes() { return $this->hasMany(CustomerNote::class); }
    public function documents() { return $this->hasMany(CustomerDocument::class); }
    public function prescriptions() { return $this->hasMany(Prescription::class); }
    public function visits() { return $this->hasMany(VisitHistory::class); }
    public function repairs() { return $this->hasMany(Repair::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
}
