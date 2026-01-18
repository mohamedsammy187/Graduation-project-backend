<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\ShoppingItem;
use App\Models\PantryItem;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();
        return ShoppingItem::where('user_id', $user->id)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string',
            'source_recipe_id' => 'nullable|exists:recipes,id'
        ]);

        // ✅ تصحيح: البحث باستخدام name_en بدلاً من name
        $ingredient = Ingredient::where('name_en', $request->item_name)
            ->orWhere('name_ar', $request->item_name)
            ->first();

        $item = ShoppingItem::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'item_name' => $request->item_name
            ],
            [
                'source_recipe_id' => $request->source_recipe_id,
                'ingredient_id' => $ingredient ? $ingredient->id : null
            ]
        );

        return response()->json([
            'status' => 'success',
            'data' => $item
        ]);
    }

    private function addToPantryAndMatch($user, $itemName)
    {
        // ✅ تصحيح: البحث باستخدام name_en بدلاً من name
        $ingredient = Ingredient::where('name_en', $itemName)
            ->orWhere('name_ar', $itemName)
            ->first();

        PantryItem::firstOrCreate([
            'user_id' => $user->id,
            'item_name' => strtolower(trim($itemName))
        ], [
            'ingredient_id' => $ingredient ? $ingredient->id : null
        ]);

        $pantry = PantryItem::where('user_id', $user->id)
            ->pluck('item_name');

        return Recipe::with('ingredients')->get()
            ->map(function ($recipe) use ($pantry) {

                $ingredients = $recipe->ingredients
                    ->map(fn($i) => strtolower($i->name)); // قد تحتاج لتعديل هذه أيضاً إلى name_en لو الموديل Ingredient لا يحتوي على Accessor

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

        if ($request->is_checked) {
            $recipes = $this->addToPantryAndMatch($user, $item->item_name);
        }

        return response()->json([
            'status' => 'updated',
            'ready_recipes' => $recipes
        ]);
    }


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

        $pantry = collect($request->pantry)
            ->map(fn($i) => strtolower(trim($i)));

        $recipeIngredients = $recipe->ingredients
            ->map(fn($i) => strtolower($i->name)); // تأكد من هذه أيضاً في الموديل

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

    public function indexWithLang(Request $request)
    {
        $lang = app()->getLocale();
        if($request->has('lang')) {
            $lang = $request->query('lang');
        }

        $user = $request->user();

        $items = ShoppingItem::with('ingredient')->where('user_id', $user->id)->get();

        $data = $items->map(function ($item) use ($lang) {
            $translatedName = null;
            
            if ($item->ingredient) {
                // ✅ تصحيح: استخدام name_en
                $translatedName = $lang === 'ar' ? $item->ingredient->name_ar : $item->ingredient->name_en;
            }

            return [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'is_checked' => $item->is_checked,
                'display_name' => $translatedName ?? $item->item_name
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
