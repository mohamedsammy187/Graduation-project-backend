<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder; // ✅ 1. Added Import

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id', 
        'title_en',
        'title_ar',
        'slug',
        'image',
        'category',    
        'meal_type',
        'cuisine',
        'temperature',
        'description_en',
        'description_ar',
        'time',
        'difficulty',
        'calories',
        'servings',
        'nutrition',
        'ingredients', 
        'steps',       
    ];

    protected $casts = [
        'nutrition' => 'array',
    ];

    /**
     * Accessor for Category
     * Return the linked category name based on current language
     */
    public function getCategoryAttribute($value)
    {
        if ($this->categoryInfo) {
            return (app()->getLocale() == 'ar') 
                ? $this->categoryInfo->name_ar 
                : $this->categoryInfo->name_en;
        }

        return $value ?: 'General';
    }

    public function categoryInfo()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_recipe')
            ->withPivot([
                'quantity',
                'unit',
                'display_text',
                'is_optional',
                'ingredient_name_ar',
                'sort_order'
            ]);
    }

    public function steps()
    {
        return $this->hasMany(Step::class)->orderBy('step_number');
    }

    public function getImageAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        if (str_starts_with($value, 'asset/')) return asset($value);
        return asset('storage/' . $value);
    }

    protected static function booted()
    {
        static::creating(function ($recipe) {
            if (empty($recipe->slug)) {
                $recipe->slug = Str::slug($recipe->title_en ?? $recipe->title_ar) . '-' . uniqid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function ask(Request $request)
    {
        $userQuestion = $request->input('prompt');
        $recipes = Recipe::with(['ingredients', 'steps'])
            ->where('title_en', 'like', "%$userQuestion%")
            ->orWhere('title_ar', 'like', "%$userQuestion%")
            ->limit(3)
            ->get();

        if ($recipes->isEmpty()) {
            return response()->json(['answer' => 'Sorry, I could not find recipes related to your question.']);
        }
        
        return response()->json(['answer' => 'Logic here']);
    }

    // =========================================================================
    // 🧠 SMART CHAT AI SCOPES (Added)
    // =========================================================================

    public function scopeSmartSearch(Builder $query, $term)
    {
        if (!$term) return $query;
        $cleanTerm = self::normalizeArabic($term);

        return $query->where(function ($q) use ($term, $cleanTerm) {
            $q->where('title_en', 'LIKE', "%{$term}%")
              ->orWhere('title_ar', 'LIKE', "%{$term}%")
              ->orWhere('title_ar', 'LIKE', "%{$cleanTerm}%")
              ->orWhereHas('ingredients', function ($subQ) use ($term, $cleanTerm) {
                  $subQ->where('name_en', 'LIKE', "%{$term}%")
                       ->orWhere('name_ar', 'LIKE', "%{$term}%")
                       ->orWhere('name_ar', 'LIKE', "%{$cleanTerm}%");
              });
        });
    }

    public function scopeApplyContext(Builder $query, $filters)
    {
        if (!empty($filters['category'])) {
            $query->where('category', 'LIKE', '%' . $filters['category'] . '%');
        }
        if (!empty($filters['meal_type'])) {
            $query->where('meal_type', 'LIKE', '%' . $filters['meal_type'] . '%');
        }
        if (!empty($filters['temperature'])) {
            $query->where('temperature', $filters['temperature']);
        }
        return $query;
    }

    public static function normalizeArabic($text)
    {
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);
        $text = str_replace('ة', 'ه', $text);
        $text = str_replace('ى', 'ي', $text);
        return $text;
    }
}