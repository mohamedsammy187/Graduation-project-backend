<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'description',
        'cat_id',
        'price',
        'quantity',
        'imagepath', 
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
    }
    // in your Product model
    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
