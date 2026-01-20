<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\ShoppingItem;
use App\Models\PantryItem;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                'ingredient_id' => $ingredient ? $ingredient->id : null,
                'is_checked' => false
            ]
        );

        return response()->json([
            'status' => 'success',
            'data' => $item
        ]);
    }

    /**
     * دالة نقل العنصر للبانتري وحساب الوصفات (تم إصلاح الخطأ هنا 🛠️)
     */
    private function addToPantryAndMatch($user, $itemName)
    {
        // 1. إضافة للبانتري
        $ingredient = Ingredient::where('name_en', $itemName)
            ->orWhere('name_ar', $itemName)
            ->first();

        PantryItem::firstOrCreate([
            'user_id' => $user->id,
            'item_name' => strtolower(trim($itemName))
        ], [
            'ingredient_id' => $ingredient ? $ingredient->id : null,
            'quantity' => '1',
            'unit' => 'unit'
        ]);

        // 2. تجهيز البانتري للمقارنة
        $pantry = PantryItem::where('user_id', $user->id)
            ->pluck('item_name')
            ->map(fn($i) => strtolower(trim(strval($i))));

        // 3. حساب الوصفات (Safe Mode)
        return Recipe::with('ingredients')->get()
            ->map(function ($recipe) use ($pantry) {
                
                // ✅ هنا الحل: نضمن إننا معانا Collection مش null
                $recipeIngredients = collect();

                // أ) نحاول نجيب العلاقة أولاً
                if ($recipe->relationLoaded('ingredients')) {
                    $recipeIngredients = $recipe->getRelation('ingredients');
                }

                // ب) لو العلاقة فاضية، نحاول نجيب من عمود JSON القديم (Fallback)
                if ($recipeIngredients->isEmpty()) {
                    $attr = $recipe->getAttribute('ingredients');
                    if (!empty($attr) && is_string($attr)) {
                        $decoded = json_decode($attr, true);
                        if (is_array($decoded)) {
                             // تحويل الشكل القديم لكائنات عشان الكود تحت يفهمها
                             $recipeIngredients = collect($decoded)->map(fn($item) => (object)[
                                 'name_en' => is_string($item) ? $item : ($item['name_en'] ?? $item['item_name'] ?? '')
                             ]);
                        }
                    }
                }

                // ج) دلوقتي نقدر نعمل map بأمان تام
                $ingredients = $recipeIngredients->map(fn($i) => 
                    strtolower(trim(strval($i->name_en ?? $i->name ?? '')))
                );

                $matched = $ingredients->intersect($pantry);
                $missing = $ingredients->diff($pantry);

                return [
                    'id' => $recipe->id,
                    'title' => $recipe->title_en ?? $recipe->title ?? 'Unknown',
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

        $isChecked = filter_var($request->is_checked, FILTER_VALIDATE_BOOLEAN);

        $item->update([
            'is_checked' => $isChecked
        ]);

        $recipes = [];

        if ($isChecked) {
            try {
                $recipes = $this->addToPantryAndMatch($user, $item->item_name);
            } catch (\Exception $e) {
                // لو حصل أي خطأ، نسجله بس منوقفش العملية عشان اليوزر ميزعلش
                Log::error("Pantry Match Error: " . $e->getMessage());
                $recipes = []; 
            }
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

    public function indexWithLang(Request $request)
    {
        $lang = $request->query('lang', 'en');
        $user = $request->user();

        $items = ShoppingItem::with('ingredient')->where('user_id', $user->id)->get();

        $data = $items->map(function ($item) use ($lang) {
            $translatedName = null;
            if ($item->ingredient) {
                $translatedName = $lang === 'ar' ? $item->ingredient->name_ar : $item->ingredient->name_en;
            }

            return [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'is_checked' => (bool)$item->is_checked,
                'display_name' => $translatedName ?? $item->item_name
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
