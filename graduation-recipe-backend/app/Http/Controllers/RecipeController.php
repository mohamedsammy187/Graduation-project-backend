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
            'data' => Recipe::select('id', 'title', 'image', 'time', 'difficulty', 'calories', 'ingredients', 'slug')->get()
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
        $pantry = $request->input('ingredients', []);
        $recipes = Recipe::all();

        $results = [];

        foreach ($recipes as $recipe) {
            $ings = json_decode($recipe->ingredients, true) ?? [];
            $matches = array_intersect(
                array_map('strtolower', $ings),
                array_map('strtolower', $pantry)
            );

            $results[] = [
                'recipe' => $recipe,
                'match_count' => count($matches),
                'matched' => array_values($matches),
            ];
        }

        usort($results, fn($a, $b) => $b['match_count'] <=> $a['match_count']);

        return response()->json($results);
    }
}
