<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function test()
    {
        return response()->json(['message' => 'API is working']);
    }

    public function index()
    {
        return response()->json([
            'data' => Recipe::select('id', 'title', 'category', 'meal_type', 'temperature', 'image', 'time', 'difficulty', 'calories' , 'slug',)->get()
        ]);
    }




    public function show($id)
    {
        $recipe = Recipe::findOrFail($id);
        return response()->json($recipe);
    }
    public function showrecipe($slug)
    {
        $recipe = Recipe::where('slug', $slug)->first();
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
    $request->validate([
        'ingredients' => 'required|array|min:1',
        'allow_missing_one' => 'boolean'
    ]);

    $allowMissingOne = $request->boolean('allow_missing_one', false);

    // Pantry ingredients → lowercase & trimmed
    $pantry = collect($request->ingredients)
        ->map(fn ($i) => strtolower(trim($i)));

    // Get recipes with ingredients relation
    $recipes = Recipe::with('ingredients')->get();

    $matchedRecipes = $recipes
        ->map(function ($recipe) use ($pantry) {

            $recipeIngredients = $recipe->ingredients
                ->map(fn ($i) => strtolower($i->name));

            $matched = $recipeIngredients->intersect($pantry);
            $missing = $recipeIngredients->diff($pantry);

            return [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
                'image' => $recipe->image,

                // 👇 IMPORTANT
                'match_count' => $matched->count(),
                'matched_ingredients' => $matched->values(),

                'missing_count' => $missing->count(),
                'missing_ingredients' => $missing->values(),
            ];
        })

        // ✅ FILTER FIRST (logic)
        ->filter(function ($recipe) use ($allowMissingOne) {

            // ❌ No matched ingredients → reject
            if ($recipe['match_count'] === 0) {
                return false;
            }

            // ✅ Fully match
            if ($recipe['missing_count'] === 0) {
                return true;
            }

            // ✅ Allow missing one (optional)
            return $allowMissingOne && $recipe['missing_count'] === 1;
        })

        // ✅ THEN SORT
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

}
