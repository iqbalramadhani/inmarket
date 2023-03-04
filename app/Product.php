<?php

namespace App;

use App\Models\Traits\MarkupPrice;
use Illuminate\Database\Eloquent\Model;
use App;

class Product extends Model {

    use MarkupPrice;

    public $noMarkupUnitPrice = false;

    protected $fillable = [
        'name', 'added_by', 'user_id', 'category_id', 'brand_id', 'video_provider', 'video_link', 'unit_price',
        'purchase_price', 'unit', 'slug', 'colors', 'choice_options', 'variations', 'thumbnail_img', 'meta_title', 'meta_description', 'weight'
    ];

    protected $casts = [
        'weight' => 'integer'
    ];

    public function getTranslation($field = '', $lang = false) {
        $lang = $lang == false ? App::getLocale() : $lang;
        $product_translations = $this->hasMany(ProductTranslation::class)->where('lang', $lang)->first();
        return $product_translations != null ? $product_translations->$field : $this->$field;
    }

    public function product_translations() {
        return $this->hasMany(ProductTranslation::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function orderDetails() {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class)->where('status', 1);
    }

    public function wishlists() {
        return $this->hasMany(Wishlist::class);
    }

    public function stocks() {
        return $this->hasMany(ProductStock::class);
    }

    // public function allQuantity() {
    //     return $this->hasMany(ProductStock::class);
    // }

    public function taxes() {
        return $this->hasMany(ProductTax::class);
    }

    public function flash_deal_product() {
        return $this->hasOne(FlashDealProduct::class);
    }

    public function bids() {
        return $this->hasMany(AuctionProductBid::class);
    }

    public function getUnitPriceAttribute($value): int
    {
        if(!$this->noMarkupUnitPrice) {
            return $this->markupPrice($value);
        } else return $value;
    }

    public function getOriginalPrice() {
        return $this->unit_price;
    }

}