<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDocument extends Model
{
    protected $fillable = ['customer_id', 'file_path', 'file_name', 'file_type', 'category', 'notes', 'uploaded_by'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
}
