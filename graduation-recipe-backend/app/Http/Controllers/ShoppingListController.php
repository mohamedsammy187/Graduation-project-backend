<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\ShoppingItem;
use Illuminate\Http\Request;
use App\Models\PantryItem;

class ShoppingListController extends Controller
{

    // GET /shopping-list
    public function index(Request $request)

    {
        $user = $request->user();
        return ShoppingItem::where('user_id', $user->id)->get();
    }

    // POST /shopping-list
    public function store(Request $request)

    {
        $request->validate([
            'item_name' => 'required|string',
            'source_recipe_id' => 'nullable|exists:recipes,id'
        ]);

        $item = ShoppingItem::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'item_name' => $request->item_name
            ],
            [
                'source_recipe_id' => $request->source_recipe_id
            ]
        );

        return response()->json([
            'status' => 'success',
            'data' => $item
        ]);
    }

    // PATCH /shopping-list/{id}
    public function toggle(Request $request, $id)
    {
        $user = $request->user();
        $item = ShoppingItem::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $item->update([
            'is_checked' => $request->is_checked
        ]);

        return response()->json(['status' => 'updated']);
    }

    // DELETE /shopping-list/{id}
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        ShoppingItem::where('user_id', $user->id)
            ->where('id', $id)
            ->delete();

        return response()->json(['status' => 'deleted']);
    }
    
    
    public function migrate(Request $request)
    {
        $request->validate([
            'slug' => 'required|string',
            'pantry' => 'required|array'
        ]);

        $recipe = Recipe::with('ingredients')
            ->where('slug', 'LIKE', $request->slug . '%')
            ->first();

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        // pantry → lowercase
        $pantry = collect($request->pantry)
            ->map(fn($i) => strtolower(trim($i)));

        // recipe ingredients from relation
        $recipeIngredients = $recipe->ingredients
            ->map(fn($i) => strtolower($i->name));

        // missing ingredients
        $missing = $recipeIngredients
            ->diff($pantry)
            ->values();

        return response()->json([
            'status' => 'success',
            'recipe' => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
            ],
            'missing' => $missing,
            'message' => 'Ready to migrate to shopping service'
        ]);
    }
}
