<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravelista\Comments\Commentable;
use Laravelista\Comments\Commenter;
use Yoeunes\Rateable\Traits\Rateable;

class Product extends Model
{
    use SoftDeletes , Commentable , Rateable;

    protected $primaryKey = 'product_id';

    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'brand_id','product_name','product_slug','status', 'data_available',
        'sku','is_off','off_price','buy_price','sale_price','made_in','description',
        'quantity','weight','cover'
    ];

    protected $guarded = ['product_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */

    public function brands()
    {
        return $this->belongsTo(brand::class,'brand_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,'category_product','product_id','category_id');
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class,'color_product','product_id','color_id');
    }

    public function photos()
    {
        return $this->morphMany(Photo::class,'products','photoable_type','photoable_id');
    }
    //get price amount of money format
//    public function getBuyPriceAttribute()
//    {
//        return number_format($this->attributes['buy_price']);
//    }

    //get created at in diffForHumans format
    public function getCreatedAtAttribute($date)
    {
        $time = new Carbon;
        return $time->diffForHumans($this->attributes['created_at']);
    }

    public function setProductSlugAttribute($value)
    {
        return $this->attributes['product_slug'] = str_slug($value);
    }

    //get cover path
    public function getCoverAttribute()
    {
        return asset(env('IMAGE_PATH').$this->attributes['cover']);
    }

    //get THUMBNAIL path
    public function getThumbnailAttribute()
    {
        return asset(env('THUMBNAIL_PATH')."T".$this->attributes['cover']);
    }

    //GET PRICE AFTER DISCOUNT
    public function getPriceAttribute()
    {
        if ($this->attributes['off_price'] == null){
            $this->attributes['off_price'] = 0 ;
        }
        return number_format($this->attributes['sale_price'] - $this->attributes['off_price']);
    }

    //GET PERCENTAGE OF OFF PRICE
    public function getOffAttribute()
    {
        $off = ($this->attributes['sale_price'] - ($this->attributes['sale_price'] - $this->attributes['off_price'])) / $this->attributes['sale_price'] * 100;
        return substr($off, 0, 3);
    }

}