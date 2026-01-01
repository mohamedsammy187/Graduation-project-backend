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

    private function addToPantryAndMatch($user, $itemName)
    {
        // ✅ Add to pantry if not exists
        PantryItem::firstOrCreate([
            'user_id' => $user->id,
            'item_name' => strtolower(trim($itemName))
        ]);

        // 🧠 Get pantry names
        $pantry = PantryItem::where('user_id', $user->id)
            ->pluck('item_name');

        // 🔥 Matching Engine (نفس اللي كتبته قبل كده)
        return Recipe::with('ingredients')->get()
            ->map(function ($recipe) use ($pantry) {

                $ingredients = $recipe->ingredients
                    ->map(fn($i) => strtolower($i->name));

                $matched = $ingredients->intersect($pantry);
                $missing = $ingredients->diff($pantry);

                return [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'slug' => $recipe->slug,
                    'missing_count' => $missing->count(),
                    'missing_ingredients' => $missing->values(),
                ];
            })
            ->filter(fn($r) => $r['missing_count'] === 0)
            ->values();
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

        $recipes = [];

        //true if check
        if ($request->is_checked) {
            $recipes = $this->addToPantryAndMatch($user, $item->item_name);
        }

        return response()->json([
            'status' => 'updated',
            'ready_recipes' => $recipes
        ]);
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
