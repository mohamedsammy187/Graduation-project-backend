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

    // Get matching ingredient IDs
    $ingredientIds = Ingredient::whereIn('name', $request->ingredients)
        ->pluck('id');       

    // Find recipes that have ANY of the ingredient IDs
    $recipes = Recipe::whereHas('ingredients', function ($q) use ($ingredientIds) {
            $q->whereIn('ingredient_id', $ingredientIds);
        })
        ->with('ingredients')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $recipes
    ]);
}

//searchGet function for second sprint "searchGet"
public function searchGet(Request $request){
    
}

}
