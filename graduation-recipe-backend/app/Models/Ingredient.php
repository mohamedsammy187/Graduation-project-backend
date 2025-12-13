<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    //Ingredient model representing ingredients in recipes
    protected $fillable = ['name'];
    
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class);
    }
}
