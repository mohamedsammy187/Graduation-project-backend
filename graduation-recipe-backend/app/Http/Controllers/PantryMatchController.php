<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class PantryMatchController extends Controller
{
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
