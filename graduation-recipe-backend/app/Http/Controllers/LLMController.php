<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Recipe;
use App\Models\ShoppingItem;

class LLMController extends Controller
{
    public function ask(Request $request)
    {
        try {
            $user = $request->user();

            // ==============================
            // 1️⃣ Get Pantry from DB
            // ==============================
            $pantry = ShoppingItem::where('user_id', $user->id)
                ->pluck('item_name')
                ->map(fn ($i) => strtolower(trim($i)));

            if ($pantry->isEmpty()) {
                return response()->json([
                    'personality' => 'friendly',
                    'answer' => 'Your pantry is empty. Add ingredients first 🧺',
                    'recipes' => []
                ]);
            }

            // ==============================
            // 2️⃣ Load Recipes + Match Engine
            // ==============================
            $recipes = Recipe::with('ingredients')->get();

            $matchedRecipes = $recipes->map(function ($recipe) use ($pantry) {

                $ingredients = $recipe->ingredients
                    ->pluck('name')
                    ->map(fn ($i) => strtolower($i));

                $matched = $ingredients->intersect($pantry);
                $missing = $ingredients->diff($pantry);

                $confidence = $ingredients->count() > 0
                    ? round($matched->count() / $ingredients->count(), 2)
                    : 0;

                return [
                    'recipe' => $recipe,
                    'matched' => $matched,
                    'missing' => $missing,
                    'confidence' => $confidence
                ];
            })
            ->filter(fn ($r) =>
                $r['confidence'] >= 0.5 && $r['missing']->count() <= 1
            )
            ->sortByDesc('confidence')
            ->values();

            if ($matchedRecipes->isEmpty()) {
                return response()->json([
                    'personality' => 'friendly',
                    'answer' => 'I couldn’t find a recipe that matches your pantry yet 🍽️',
                    'recipes' => []
                ]);
            }

            // ==============================
            // 3️⃣ Pick Best Recipe
            // ==============================
            $best = $matchedRecipes->first();
            $recipe = $best['recipe'];

            // ==============================
            // 4️⃣ Auto-add missing ingredients to shopping list
            // ==============================
            foreach ($best['missing'] as $missingIngredient) {
                ShoppingItem::firstOrCreate([
                    'user_id' => $user->id,
                    'item_name' => $missingIngredient
                ]);
            }

            // ==============================
            // 5️⃣ Build Stable JSON Response
            // ==============================
            return response()->json([
                'personality' => 'friendly',
                'confidence_score' => $best['confidence'],
                'answer' => "I recommend **{$recipe->title}** based on your pantry 🥗",
                'recipes' => [
                    [
                        'id' => $recipe->id,
                        'title' => $recipe->title,
                        'slug' => $recipe->slug,
                        'image' => $recipe->image,
                        'calories' => $recipe->calories,
                        'difficulty' => $recipe->difficulty,
                        'time' => $recipe->time,
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
