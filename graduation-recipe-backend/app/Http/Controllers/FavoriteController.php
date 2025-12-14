<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    private $userId = 1; // static for now

    // GET /favorites
    public function index()
    {
        return Favorite::with('recipe')
            ->where('user_id', $this->userId)
            ->get();
    }

    // POST /favorites
    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id'
        ]);

        $favorite = Favorite::firstOrCreate([
            'user_id' => $this->userId,
            'recipe_id' => $request->recipe_id
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $favorite
        ]);
    }

    // DELETE /favorites/{recipe_id}
    public function destroy($recipe_id)
    {
        Favorite::where('user_id', $this->userId)
            ->where('recipe_id', $recipe_id)
            ->delete();

        return response()->json(['status' => 'deleted']);
    }
}
