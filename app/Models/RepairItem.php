<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairItem extends Model
{
    protected $fillable = ['repair_id', 'repair_type', 'price'];

    public function repair() 
    { 
        return $this->belongsTo(Repair::class); 
    }
}
