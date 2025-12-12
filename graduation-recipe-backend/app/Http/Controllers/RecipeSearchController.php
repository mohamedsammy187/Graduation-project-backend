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
            $q->whereIn('name', $ingredientIds);
        })
            ->with('ingredients')
            ->get();

        return $recipes;
    }
}
