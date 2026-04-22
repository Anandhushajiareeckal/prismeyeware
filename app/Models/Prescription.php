<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'customer_id', 'prescription_date', 'recall_date', 'type', 'doctor_name',
        'od_sphere', 'od_cylinder', 'od_axis', 'od_h_prism', 'od_v_prism', 'od_add', 'od_pd', 'od_fh',
        'os_sphere', 'os_cylinder', 'os_axis', 'os_h_prism', 'os_v_prism', 'os_add', 'os_pd', 'os_fh',
        'comments', 'created_by'
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
