<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class CategoryTranslation extends Model
{
    use Cachable;
    protected $cachePrefix = "category_trans_cache";
    protected $cacheCooldownSeconds = 300;

    protected $fillable = ['name', 'lang', 'category_id'];

    public function category(){
    	return $this->belongsTo(Category::class);
    }
}
