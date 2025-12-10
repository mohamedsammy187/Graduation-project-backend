<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'title',
        'category',
        'image',
        'time',
        'difficulty',
        'calories',
        'ingredients',
        'steps',
    ];
}
