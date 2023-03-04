<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplainImage extends Model
{
    protected $fillable = [];
    protected $guarded = [];

    public function detail() {
        return $this->belongsTo(\App\Upload::class, 'thumbnail_img', 'id')->first();
    }
}
