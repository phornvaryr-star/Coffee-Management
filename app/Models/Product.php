<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
class Product extends Model
{
    protected $fillable = [
        'category_id',
        'product_name',
        'purchase_price',
        'sale_price',
        'qty',
        'image',
        'description',
        'status'
    ];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->updated_at = null;
        });
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
