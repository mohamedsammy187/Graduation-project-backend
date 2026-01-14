<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class RecipeSearchController extends Controller
{
public function search(Request $request)
{
    $q = $request->query('q');
    $ingredients = $request->query('ingredients');
    $category = $request->query('category');
    $mealType = $request->query('meal_type');
    $temperature = $request->query('temperature');

    $query = Recipe::query();

    if ($q) {
        $query->where('title', 'like', "%$q%");
    }

    if ($category) {
        $query->where('category', $category);
    }

    if ($mealType) {
        $query->where('meal_type', $mealType);
    }

    if ($temperature) {
        $query->where('temperature', $temperature);
    }

    if ($ingredients) {
        $ings = is_string($ingredients)
            ? explode(',', $ingredients)
            : $ingredients;

        $query->whereHas('ingredients', function ($q) use ($ings) {
            $q->whereIn(DB::raw('LOWER(name)'), array_map('strtolower',$ings));

        });
    }

    $recipes = $query->with('ingredients')->get();

    return response()->json([
        'status' => 'success',
        'count' => $recipes->count(),
        'data' => $recipes
    ]);
}

}
