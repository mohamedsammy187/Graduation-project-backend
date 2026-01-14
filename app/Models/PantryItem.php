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
}
