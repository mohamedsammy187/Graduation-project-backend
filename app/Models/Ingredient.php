<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    //Ingredient model representing ingredients in recipes
    protected $fillable = [
        'name_en',
        'name_ar',
        'category',
        'image'
    ];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class);
    }

    public function getNameAttribute()
    {
        logger('NAME ACCESSOR', [
            'locale' => app()->getLocale(),
            'ar' => $this->name_ar,
            'en' => $this->name_en,
        ]);

        return app()->getLocale() === 'ar'
            ? ($this->name_ar ?? $this->name_en)
            : ($this->name_en ?? $this->name_ar);
    }
}
