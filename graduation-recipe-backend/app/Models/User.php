<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Favorites
    public function favorites()
    {
        return $this->belongsToMany(Recipe::class, 'favorites')
                    ->withPivot('saved_at');
    }

    // Pantry
    public function pantryItems()
    {
        return $this->hasMany(PantryItem::class);
    }

    // Shopping list
    public function shoppingItems()
    {
        return $this->hasMany(ShoppingItem::class);
    }
}
