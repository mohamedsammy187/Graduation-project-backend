<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    use HasFactory;

    // ده "التصريح" اللي كان ناقص
    protected $fillable = [
        'recipe_id',
        'step_number',
        'instruction_en',
        'instruction_ar',
    ];

    // علاقة عكسية (عشان لو احتاجنا نجيب الوصفة من الخطوة)
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}