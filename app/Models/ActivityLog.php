<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['action', 'entity_type', 'entity_id', 'user_id', 'details'];

    public function user() { return $this->belongsTo(User::class); }
    
    public function entity() { return $this->morphTo(); }
}
