<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Recipe extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'image',
        'category',
        'meal_type',
        'temperature',
        'time',
        'difficulty',
        'calories'
    ];


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
            ])
            ->orderBy('ingredient_recipe.sort_order');
    }


    public function ask(Request $request)
    {
        $userQuestion = $request->input('prompt');

        // 1. Fetch relevant recipes from DB
        $recipes = Recipe::where('title', 'like', "%$userQuestion%")
            ->orWhereJsonContains('ingredients', $userQuestion)
            ->limit(5)
            ->get();

        if ($recipes->isEmpty()) {
            return response()->json([
                'answer' => 'Sorry, I could not find recipes related to your question in our database.'
            ]);
        }

        // 2. Build context from DB
        $context = "You are a recipe assistant. Answer ONLY using the data below.\n\n";

        foreach ($recipes as $r) {
            $context .= "Title: {$r->title}\n";
            $context .= "Ingredients: " . implode(', ', json_decode($r->ingredients, true)) . "\n";
            $context .= "Steps: " . implode(' | ', json_decode($r->steps, true)) . "\n\n";
        }

        $finalPrompt = $context . "User question: " . $userQuestion;

        // 3. Send to Gemini
        $response = Http::post(
            env('GEMINI_API_URL') . '?key=' . env('GEMINI_API_KEY'),
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $finalPrompt]
                        ]
                    ]
                ]
            ]
        );

        return response()->json($response->json(), $response->status());
    }

    protected static function booted()
    {
        static::creating(function ($recipe) {
            if (empty($recipe->slug)) {
                $recipe->slug = Str::slug($recipe->title) . '-' . uniqid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTitleAttribute()
    {
        logger('TITLE ACCESSOR', [
            'locale' => app()->getLocale(),
            'ar' => $this->title_ar,
            'en' => $this->title_en,
        ]);

        return app()->getLocale() === 'ar'
            ? ($this->title_ar ?? $this->title_en)
            : ($this->title_en ?? $this->title_ar);
    }

    public function getDescriptionAttribute()
    {
        logger('TITLE ACCESSOR', [
            'locale' => app()->getLocale(),
            'ar' => $this->title_ar,
            'en' => $this->title_en,
        ]);
        return app()->getLocale() === 'ar'
            ? ($this->description_ar ?? $this->description_en)
            : ($this->description_en ?? $this->description_ar);
    }
}
