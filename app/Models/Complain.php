<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    //
    protected $fillable = [];
    protected $guarded = [];

    public function images() {
        return $this->hasMany(\App\Models\ComplainImage::class, 'complain_id', 'id');
    }

    public function getImageString() {
        $images = $this->images()->get();
        $imageArray = [];
        foreach($images as $image) {
            $imageArray[] = $image->thumbnail_img;
        }
        return implode(",", $imageArray);
    }

    public function order_detail() {
        return $this->hasOne(\App\OrderDetail::class, 'id', 'order_detail_id');
    }
    
    public function order() {
        return $this->hasOne(\App\Order::class, 'id', 'order_id');
    }
}
