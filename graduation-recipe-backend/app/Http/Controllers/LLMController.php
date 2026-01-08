<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\PantryItem;
use App\Models\ShoppingItem;

class LLMController extends Controller
{
    public function ask(Request $request)
    {
        $user = $request->user();
        $message = strtolower(trim($request->input('prompt')));

        /*
        |--------------------------------------------------------------------------
        | Normalize Arabic & English
        |--------------------------------------------------------------------------
        */
        $normalized = $this->normalizeText($message);

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Inquiry (Pantry / Shopping List)
        |--------------------------------------------------------------------------
        */
        if ($this->isInquiry($normalized)) {
            return $this->handleInquiry($normalized, $user);
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Recipe Detail Questions (ingredients / steps / time)
        |--------------------------------------------------------------------------
        */
        if ($this->isRecipeDetailQuestion($normalized)) {
            return $this->handleRecipeDetails($normalized);
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Ingredient-based Search (DB FIRST)
        |--------------------------------------------------------------------------
        */
        if ($this->mentionsIngredients($normalized)) {
            return $this->searchByIngredients($normalized);
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Pantry Mode (Smart Matching)
        |--------------------------------------------------------------------------
        */
        if ($this->isPantryIntent($normalized)) {
            return $this->pantryMatch($user);
        }

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ Keyword Filtering (existing logic)
        |--------------------------------------------------------------------------
        */
        if ($this->hasKeywords($normalized)) {
            return $this->filterByKeywords($normalized);
        }

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ General Recommendation
        |--------------------------------------------------------------------------
        */
        if ($this->isGeneralRecommendation($normalized)) {
            return $this->generalRecommendation();
        }

        /*
        |--------------------------------------------------------------------------
        | 7️⃣ Chit-chat (Fallback)
        |--------------------------------------------------------------------------
        */
        return $this->chitChat();
    }

    /* ============================================================
     | 🧠 Language Helpers
     ============================================================ */

    private function normalizeText($text)
    {
        return str_replace(
            ['؟', 'إ', 'أ', 'آ'],
            ['?', 'ا', 'ا', 'ا'],
            $text
        );
    }

    /* ============================================================
     | 1️⃣ Inquiry
     ============================================================ */

    private function isInquiry($text)
    {
        return str_contains($text, 'عندي')
            || str_contains($text, 'pantry')
            || str_contains($text, 'shopping')
            || str_contains($text, 'شوبينج')
            || str_contains($text, 'ناقص');
    }

    private function handleInquiry($text, $user)
    {
        if (str_contains($text, 'عندي') || str_contains($text, 'pantry')) {
            return response()->json([
                'type' => 'pantry',
                'items' => PantryItem::where('user_id', $user->id)->pluck('item_name')
            ]);
        }

        if (str_contains($text, 'shopping') || str_contains($text, 'شوبينج')) {
            return response()->json([
                'type' => 'shopping',
                'items' => ShoppingItem::where('user_id', $user->id)->pluck('item_name')
            ]);
        }

        return response()->json([
            'message' => 'تحب تسأل عن وصفة معينة؟ 🍽️'
        ]);
    }

    /* ============================================================
     | 2️⃣ Recipe Details
     ============================================================ */

    private function isRecipeDetailQuestion($text)
    {
        return str_contains($text, 'مكونات')
            || str_contains($text, 'ingredients')
            || str_contains($text, 'خطوات')
            || str_contains($text, 'steps')
            || str_contains($text, 'وقت')
            || str_contains($text, 'time');
    }

    private function handleRecipeDetails($text)
    {
        $recipe = Recipe::where(function ($q) use ($text) {
            $q->whereRaw("LOWER(title) LIKE ?", ["%$text%"]);
        })->with('ingredients')->first();

        if (!$recipe) {
            return response()->json([
                'message' => 'لم أجد الوصفة المطلوبة ❌'
            ]);
        }

        if (str_contains($text, 'مكونات') || str_contains($text, 'ingredients')) {
            return $recipe->ingredients->pluck('name');
        }

        if (str_contains($text, 'خطوات') || str_contains($text, 'steps')) {
            return $recipe->steps;
        }

        if (str_contains($text, 'وقت') || str_contains($text, 'time')) {
            return $recipe->time;
        }

        return null;
    }

    /* ============================================================
     | 3️⃣ Ingredient-based Search
     ============================================================ */

    private function mentionsIngredients($text)
    {
        return Ingredient::whereRaw("LOWER(?) LIKE CONCAT('%', name, '%')", [$text])->exists();
    }

    private function searchByIngredients($text)
    {
        $ingredientIds = Ingredient::whereRaw("LOWER(?) LIKE CONCAT('%', name, '%')", [$text])
            ->pluck('id');

        $recipes = Recipe::whereHas('ingredients', function ($q) use ($ingredientIds) {
            $q->whereIn('ingredients.id', $ingredientIds);
        })->with('ingredients')->get();

        return response()->json([
            'mode' => 'ingredient_search',
            'recipes' => $recipes
        ]);
    }

    /* ============================================================
     | 4️⃣ Pantry Mode (Smart Matching)
     ============================================================ */

    private function isPantryIntent($text)
    {
        return str_contains($text, 'pantry')
            || str_contains($text, 'عندي')
            || str_contains($text, 'from my ingredients');
    }

    private function pantryMatch($user)
    {
        $pantry = PantryItem::where('user_id', $user->id)
            ->pluck('item_name')
            ->map(fn($i) => strtolower($i));

        $recipes = Recipe::with('ingredients')->get();

        $matched = $recipes->map(function ($recipe) use ($pantry) {
            $ingredients = $recipe->ingredients->pluck('name')->map(fn($i) => strtolower($i));
            $matched = $ingredients->intersect($pantry);
            $missing = $ingredients->diff($pantry);

            return [
                'recipe' => $recipe,
                'confidence' => round($matched->count() / max($ingredients->count(), 1), 2),
                'missing' => $missing
            ];
        })
        ->filter(fn($r) => $r['confidence'] >= 0.5)
        ->sortByDesc('confidence')
        ->values();

        return response()->json([
            'mode' => 'pantry',
            'results' => $matched,
            'note' => 'تحب أضيف المكونات الناقصة للشوبينج ليست؟'
        ]);
    }

    /* ============================================================
     | 5️⃣ Keyword Filtering
     ============================================================ */

    private function hasKeywords($text)
    {
        return preg_match('/(fish|chicken|حلو|فطار|سمك|عشا)/', $text);
    }

    private function filterByKeywords($text)
    {
        return Recipe::whereRaw("LOWER(title) LIKE ?", ["%$text%"])
            ->orWhereHas('ingredients', function ($q) use ($text) {
                $q->whereRaw("LOWER(name) LIKE ?", ["%$text%"]);
            })
            ->get();
    }

    /* ============================================================
     | 6️⃣ General Recommendation
     ============================================================ */

    private function isGeneralRecommendation($text)
    {
        return in_array($text, [
            'suggest food',
            'عايز اكل',
            'hungry',
            'recommend'
        ]);
    }

    private function generalRecommendation()
    {
        return Recipe::inRandomOrder()->first();
    }

    /* ============================================================
     | 7️⃣ Chit-chat
     ============================================================ */

    private function chitChat()
    {
        return response()->json([
            'message' => '👋 أهلاً! قولي تحب تطبخ ايه أو اكتب المكونات اللي عندك'
        ]);
    }
}
