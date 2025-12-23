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
            'data' => Recipe::select('id', 'title', 'image', 'time', 'difficulty', 'calories', 'ingredients','slug')->get()
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
        if(!$recipe){
            return response()->json(['message' => 'Recipe not found'], 404);

        }
        return $recipe;
    }

    public function store(Request $request)
    {
        $recipe = Recipe::create($request->all());
        return response()->json($recipe, 201);
    }
}
