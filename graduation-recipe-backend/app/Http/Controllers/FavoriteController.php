<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{


    // GET /favorites
    public function index(Request $request)
    {
        return Favorite::with('recipe.ingredients')
            ->where('user_id', $request->user()->id)
            ->get();
    }

    // POST /favorites
    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id'
        ]);

        $favorite = Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'recipe_id' => $request->recipe_id
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $favorite
        ]);
    }

    // DELETE /favorites/{recipe_id}
    public function destroy(Request $request, $recipe_id)
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('recipe_id', $recipe_id)
            ->delete();

        return response()->json(['status' => 'deleted']);
    }
}
