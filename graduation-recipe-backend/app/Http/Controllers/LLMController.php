<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Recipe;
use App\Models\PantryItem;
use App\Models\ShoppingItem;

class LLMController extends Controller
{
    public function ask(Request $request)
    {
        try {
            $user = $request->user();
            $userPrompt = strtolower(trim($request->input('prompt')));

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ Friendly greetings (no DB, no AI cost)
            |--------------------------------------------------------------------------
            */
            if (preg_match('/^(hi|hello|hey|السلام|مرحبا)/i', $userPrompt)) {
                return response()->json([
                    'personality' => 'friendly',
                    'answer' => 'Hello! 👋 I can help you with recipes, pantry items, or shopping lists. What would you like to cook today?',
                    'recipes' => []
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 2️⃣ Load Pantry (SOURCE OF TRUTH)
            |--------------------------------------------------------------------------
            */
            $pantry = PantryItem::where('user_id', $user->id)
                ->pluck('item_name')
                ->map(fn ($i) => strtolower(trim($i)));

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ Load Recipes
            |--------------------------------------------------------------------------
            */
            $recipes = Recipe::with('ingredients')->get();

            if ($recipes->isEmpty()) {
                return response()->json([
                    'personality' => 'friendly',
                    'answer' => 'No recipes are available yet.',
                    'recipes' => []
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 4️⃣ Match Engine (Laravel brain 🧠)
            |--------------------------------------------------------------------------
            */
            $matchedRecipes = $recipes->map(function ($recipe) use ($pantry) {

                $ingredients = $recipe->ingredients
                    ->pluck('name')
                    ->map(fn ($i) => strtolower(trim($i)));

                $matched = $ingredients->intersect($pantry);
                $missing = $ingredients->diff($pantry);

                $confidence = $ingredients->count() > 0
                    ? round($matched->count() / $ingredients->count(), 2)
                    : 0;

                return [
                    'recipe' => $recipe,
                    'matched' => $matched,
                    'missing' => $missing,
                    'confidence' => $confidence,
                ];
            })
            // 🎯 rules
            ->filter(fn ($r) => $r['confidence'] >= 0.5)
            ->filter(fn ($r) => $r['missing']->count() <= 1)
            ->sortByDesc('confidence')
            ->values();

            if ($matchedRecipes->isEmpty()) {
                return response()->json([
                    'personality' => 'friendly',
                    'answer' => 'I couldn’t find a recipe that matches your pantry yet 🍽️',
                    'recipes' => []
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 5️⃣ Pick Best Recipe
            |--------------------------------------------------------------------------
            */
            $best = $matchedRecipes->first();
            $recipe = $best['recipe'];

            /*
            |--------------------------------------------------------------------------
            | 6️⃣ Auto-add missing ingredients to Shopping List
            |--------------------------------------------------------------------------
            */
            foreach ($best['missing'] as $item) {
                ShoppingItem::firstOrCreate([
                    'user_id' => $user->id,
                    'item_name' => $item
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 7️⃣ Build AI-style response (controlled)
            |--------------------------------------------------------------------------
            */
            $answerText = $best['missing']->isEmpty()
                ? "Great news! You can make **{$recipe->title}** with what you already have 🎉"
                : "I recommend **{$recipe->title}**! You’re only missing one ingredient, so I added it to your shopping list 🛒";

            /*
            |--------------------------------------------------------------------------
            | 8️⃣ Final Stable JSON
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'personality' => 'friendly',
                'confidence_score' => $best['confidence'],
                'answer' => $answerText,
                'recipes' => [
                    [
                        'id' => $recipe->id,
                        'title' => $recipe->title,
                        'slug' => $recipe->slug,
                        'image' => $recipe->image,
                        'time' => $recipe->time,
                        'difficulty' => $recipe->difficulty,
                        'calories' => $recipe->calories,
                        'link' => rtrim(config('app.url'), '/') . "/api/recipes/slug/{$recipe->slug}",
                        'ingredients' => $recipe->ingredients->pluck('name'),
                        'missing_ingredients' => $best['missing']->values(),
                        'confidence_score' => $best['confidence']
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'personality' => 'friendly',
                'answer' => 'Something went wrong, please try again.',
                'recipes' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
