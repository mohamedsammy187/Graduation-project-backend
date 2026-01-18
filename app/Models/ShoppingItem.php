<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingItem extends Model
{
    protected $fillable = [
        'user_id',
        'item_name',
        'is_checked',
        'source_recipe_id',
        'ingredient_id', // ✅ تمت الإضافة هنا
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'source_recipe_id');
    }
    
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }
}
