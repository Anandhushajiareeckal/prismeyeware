<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id', 'item_name', 'sku', 'quantity',
        'rate', 'tax', 'discount', 'total'
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
