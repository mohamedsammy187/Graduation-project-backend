<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'recipe_id',
        'saved_at',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
