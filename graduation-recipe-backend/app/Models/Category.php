<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'name_en',
        'name_ar',
        'description',
        'imagepath',  // fixed!
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'cat_id');
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar'
            ? $this->name_ar
            : $this->name_en;
    }
}
