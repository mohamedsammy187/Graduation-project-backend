<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\PantryItem;
use App\Models\ShoppingItem;

class LLMController extends Controller
{
    public function ask(Request $request)
    {
        try {
            $user = $request->user();
            $userPrompt = trim($request->input('prompt'));

            if (empty($userPrompt)) {
                return response()->json([
                    'personality' => 'friendly',
                    'answer' => 'Hi there! 🍳 Please tell me what you want to cook or know about your pantry.',
                    'recipes' => []
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ Friendly greetings
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
            | 2️⃣ Load full pantry
            |--------------------------------------------------------------------------
            */
            $pantry = PantryItem::where('user_id', $user->id)
                ->pluck('item_name')
                ->map(fn($i) => strtolower(trim($i)));

            /*
            |--------------------------------------------------------------------------
            | 3️⃣ Load full recipes DB
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
            | 4️⃣ Extract keywords from user question
            |--------------------------------------------------------------------------
            */
            $keywords = collect(explode(' ', strtolower($userPrompt)))
                ->map(fn($w) => trim($w, " ?!.,"))
                ->filter(fn($w) => strlen($w) > 2);

            /*
            |--------------------------------------------------------------------------
            | 5️⃣ Filter recipes by keywords
            |--------------------------------------------------------------------------
            */
            $filteredRecipes = $recipes->filter(function ($recipe) use ($keywords) {

                $title = strtolower($recipe->title);
                $ingredients = $recipe->ingredients
                    ->pluck('name')
                    ->map(fn($i) => strtolower($i));

                foreach ($keywords as $word) {
                    if (str_contains($title, $word) || $ingredients->contains($word)) {
                        return true;
                    }
                }

                return false;
            });

            // fallback: use full DB if keyword filter returns empty
            if ($filteredRecipes->isEmpty()) {
                $filteredRecipes = $recipes;
            }

            /*
            |--------------------------------------------------------------------------
            | 6️⃣ Match recipes with pantry
            |--------------------------------------------------------------------------
            */
            $matchedRecipes = $filteredRecipes->map(function ($recipe) use ($pantry) {

                $ingredients = $recipe->ingredients
                    ->pluck('name')
                    ->map(fn($i) => strtolower(trim($i)));

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
            ->filter(fn($r) => $r['confidence'] >= 0.5)
            ->filter(fn($r) => $r['missing']->count() <= 1)
            ->sortByDesc('confidence')
            ->values();

            /*
            |--------------------------------------------------------------------------
            | 7️⃣ Select best recipe
            |--------------------------------------------------------------------------
            */
            if ($matchedRecipes->isEmpty()) {
                return response()->json([
                    'personality' => 'friendly',
                    'answer' => 'I couldn’t find a recipe that matches your pantry yet 🍽️',
                    'recipes' => []
                ]);
            }

            $best = $matchedRecipes->first();
            $recipe = $best['recipe'];

            /*
            |--------------------------------------------------------------------------
            | 8️⃣ Auto-add missing ingredients to Shopping List
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
            | 9️⃣ Build AI-style response
            |--------------------------------------------------------------------------
            */
            $answerText = $best['missing']->isEmpty()
                ? "Great news! You can make **{$recipe->title}** with what you already have 🎉"
                : "I recommend **{$recipe->title}**! You’re only missing one ingredient, so I added it to your shopping list 🛒";

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
                        'steps' => json_decode($recipe->steps, true),
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
