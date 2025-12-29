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
            'data' => Recipe::select('id', 'title','catregory','meal_type','temperature', 'image', 'time', 'difficulty', 'calories', 'ingredients', 'slug',)->get()
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
    $pantry = array_map('strtolower', $request->input('ingredients', []));
    $allowMissingOne = $request->boolean('allow_missing_one', false);

    $recipes = Recipe::all();
    $results = [];

    foreach ($recipes as $recipe) {

        $ingsRaw = json_decode($recipe->ingredients, true);
        if (!is_array($ingsRaw)) continue;

        // تنظيف المكونات
        $recipeIngs = array_map(function ($ing) {
            $ing = strtolower($ing);
            $ing = preg_replace('/[^a-z ]/', '', $ing);
            return trim($ing);
        }, $ingsRaw);

        $recipeIngs = array_filter($recipeIngs);

        $matched = array_intersect($recipeIngs, $pantry);
        $missing = array_diff($recipeIngs, $pantry);

        $missingCount = count($missing);

        // 🔥 الفلترة الصح
        if ($missingCount === 0 || ($allowMissingOne && $missingCount === 1)) {
            $results[] = [
                'recipe' => $recipe,
                'matched' => array_values($matched),
                'missing' => array_values($missing),
                'missing_count' => $missingCount
            ];
        }
    }

    return response()->json([
        'status' => 'success',
        'count' => count($results),
        'data' => $results
    ]);
}

}
