<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PantryItem extends Model
{
    protected $fillable = [
        'user_id',
        'item_name',
        'ingredient_id',
        'added_date'
    ];

    // ✅ التعديل هنا: شيل حرف الـ s عشان تبقى مفرد زي ما الكونترولر عاوزها
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }
}