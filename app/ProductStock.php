<?php

namespace App;

use App\Models\Traits\MarkupPrice;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use MarkupPrice;

    protected $fillable = ['product_id', 'qty', 'price'];
    //
    public function product(){
    	return $this->belongsTo(Product::class);
    }

    public function getPriceAttribute($value): int
    {
        return $this->markupPrice($value);
    }
}
