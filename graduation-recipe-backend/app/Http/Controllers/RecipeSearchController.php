<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;

use Illuminate\Http\Request;

class RecipeSearchController extends Controller
{
    //search function for second sprint
    public function search(Request $request)
    {
        $request->validate([
            'ingredients' => 'required|array',
        ]);

        $ingredientIds = Ingredient::whereIn('name', $request->ingredients)
            ->pluck('id');

        $recipes = Recipe::whereHas('ingredients', function ($q) use ($ingredientIds) {
            $q->whereIn('ingredients.id', $ingredientIds);
        })
            ->with('ingredients')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $recipes
        ]);
    }


    //searchGet function for second sprint "searchGet"
    public function searchGet(Request $request)
    {
        $ingredients = $request->query('ingredients');

        if (!$ingredients) {
            return response()->json([
                'status' => 'error',
                'message' => 'ingredients required'
            ], 400);
        }

        if (is_string($ingredients)) {
            $ingredients = explode(',', $ingredients);
        }

        $ingredientIds = Ingredient::whereIn('name', $ingredients)->pluck('id');

        $recipes = Recipe::whereHas('ingredients', function ($q) use ($ingredientIds) {
            $q->whereIn('ingredients.id', $ingredientIds);
        })
            ->with('ingredients')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $recipes
        ]);
    }
}
