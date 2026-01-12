<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use App\Models\PantryItem;
use App\Models\ShoppingItem;

class RecipeController extends Controller
{
    public function test()
    {
        return response()->json(['message' => 'API is working']);
    }



    public function index()
    {
        $recipes = Recipe::with('ingredients')->paginate(12);

        return response()->json([
            'data' => $recipes->map(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'slug' => $recipe->slug,
                    'time' => $recipe->time,
                    'difficulty' => $recipe->difficulty,
                    'calories' => $recipe->calories,
                    'image' => $recipe->image,
                    'ingredients' => $recipe->ingredients->map(fn($i) => [
                        'id' => $i->id,
                        'name' => $i->name,
                    ]),
                    'steps' => json_decode($recipe->steps, true),
                ];
            })
        ]);
    }


    public function show($id)
    {
        $recipe = Recipe::with('ingredients')->findOrFail($id);

        return response()->json([
            'id' => $recipe->id,
            'title' => $recipe->title,
            'slug' => $recipe->slug,
            'time' => $recipe->time,
            'difficulty' => $recipe->difficulty,
            'calories' => $recipe->calories,
            'image' => $recipe->image,
            'ingredients' => $recipe->ingredients->map(fn($i) => [
                'id' => $i->id,
                'name' => $i->name,
            ]),
            'steps' => json_decode($recipe->steps, true),
        ]);
    }




    public function showrecipe($slug)
    {
        $recipe = Recipe::with('ingredients')->where('slug', $slug)->first();

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }
        return $recipe;
    }

    public function store(Request $request)
    {
        $recipe = Recipe::create($request->all());
        return response()->json($recipe, 201);
    }





    public function matchPantry(Request $request)
    {
        $user = $request->user();

        // 🧺 1. Get pantry from DB
        $pantry = PantryItem::where('user_id', $user->id)
            ->pluck('item_name')
            ->map(fn($i) => strtolower(trim($i)));

        if ($pantry->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'count' => 0,
                'data' => [],
                'message' => 'Pantry is empty'
            ]);
        }

        // 🔧 optional flag from query
        // /recipes/match-pantry?allow_missing_one=true
        $allowMissingOne = filter_var(
            $request->query('allow_missing_one', false),
            FILTER_VALIDATE_BOOLEAN
        );

        // 🍽 2. Load recipes
        $recipes = Recipe::with('ingredients');
        //  filters for matching
        if ($request->query('keyword')) {
            $keyword = $request->query('keyword');
            $recipes = $recipes->where('title', 'like', "%{$keyword}%");
        }
        if ($request->query('difficulty')) {
            $difficulty = $request->query('difficulty');
            $recipes = $recipes->where('difficulty', $difficulty);
        }
        if ($request->query('max_time')) {
            $maxTime = $request->query('max_time');
            $recipes = $recipes->where('time', '<=', $maxTime);
        }
        $recipes = $recipes->get();
        // 🧠 Match Logic
        $matchedRecipes = $recipes
            ->map(function ($recipe) use ($pantry) {

                $recipeIngredients = $recipe->ingredients
                    ->pluck('name')
                    ->map(fn($i) => strtolower($i));

                $matched = $recipeIngredients->intersect($pantry);
                $missing = $recipeIngredients->diff($pantry);

                return [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'slug' => $recipe->slug,
                    'image' => $recipe->image,
                    'difficulty' => $recipe->difficulty,
                    'time' => $recipe->time,
                    'calories' => $recipe->calories,
                    'steps' => json_decode($recipe->steps, true),

                    'match_count' => $matched->count(),
                    'matched_ingredients' => $matched->values(),

                    'missing_count' => $missing->count(),
                    'missing_ingredients' => $missing->values(),
                ];
            })

            // 🧠 3. Filter logic
            ->filter(function ($recipe) use ($allowMissingOne) {

                if ($recipe['match_count'] === 0) {
                    return false;
                }

                if ($recipe['missing_count'] === 0) {
                    return true;
                }

                return $allowMissingOne && $recipe['missing_count'] === 1;
            })

            // 📊 4. Sort
            ->sortBy([
                ['missing_count', 'asc'],
                ['match_count', 'desc'],
            ])
            ->values();

        return response()->json([
            'status' => 'success',
            'count' => $matchedRecipes->count(),
            'data' => $matchedRecipes
        ]);
    }




    public function surpriseMe(Request $request)
    {
        $user = $request->user();

        // 1️⃣ Get pantry from shopping_items
        $pantry = PantryItem::where('user_id', $user->id)
            ->pluck('item_name')
            ->map(fn($i) => strtolower(trim($i)));

        if ($pantry->isEmpty()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Your pantry is empty. Add ingredients first.'
            ], 400);
        }

        // 2️⃣ Load recipes
        $recipes = Recipe::with('ingredients')->get();

        $matched = $recipes
            ->filter(fn($r) => $r->id !== $user->last_surprise_recipe_id)
            ->map(function ($recipe) use ($pantry) {

                $ingredients = $recipe->ingredients
                    ->pluck('name')
                    ->map(fn($i) => strtolower($i));

                $matched = $ingredients->intersect($pantry);
                $missing = $ingredients->diff($pantry);

                return [
                    'recipe' => $recipe,
                    'matched_count' => $matched->count(),
                    'missing_count' => $missing->count(),
                    'missing' => $missing
                ];
            })

            // ✅ must match at least 1 ingredient
            ->filter(fn($r) => $r['matched_count'] > 0)

            // 🎲 allow missing one
            ->filter(fn($r) => $r['missing_count'] <= 1)
            ->values();

        if ($matched->isEmpty()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No suitable recipe found with your pantry.'
            ]);
        }

        // 3️⃣ Pick random recipe
        $selected = $matched->random();
        $recipe = $selected['recipe'];

        // 4️⃣ Save last suggested recipe
        $user->update([
            'last_surprise_recipe_id' => $recipe->id
        ]);

        // 5️⃣ Response
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
                'image' => $recipe->image,
                'time' => $recipe->time,
                'difficulty' => $recipe->difficulty,
                'calories' => $recipe->calories,

                'ingredients' => $recipe->ingredients->map(fn($i) => [
                    'id' => $i->id,
                    'name' => $i->name
                ]),

                'missing_ingredients' => $selected['missing']->values(),

                // ✅ deployment-safe link
                'link' => rtrim(config('app.url'), '/') . "/api/recipes/slug/{$recipe->slug}",
            ]
        ]);
    }
}
