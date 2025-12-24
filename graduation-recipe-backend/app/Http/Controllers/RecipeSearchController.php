<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class RecipeSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = Recipe::query()->with('ingredients');

        // 🔎 Search by title
        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }

        // 🥕 Search by ingredients
        if ($request->filled('ingredients')) {
            $ingredients = $request->ingredients;

            if (is_string($ingredients)) {
                $ingredients = explode(',', $ingredients);
            }

            $ingredientIds = Ingredient::whereIn('name', $ingredients)->pluck('id');

            $query->whereHas('ingredients', function ($q) use ($ingredientIds) {
                $q->whereIn('ingredients.id', $ingredientIds);
            });
        }

        // 🗂 Category filter (only if column exists)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // 🍽 Meal type filter
        if ($request->filled('meal_type')) {
            $query->where('meal_type', $request->meal_type);
        }

        // 🌡 Temperature filter
        if ($request->filled('temperature')) {
            $query->where('temperature', $request->temperature);
        }

        $recipes = $query->get();

        return response()->json([
            'status' => 'success',
            'count' => $recipes->count(),
            'data' => $recipes
        ]);
    }
}
