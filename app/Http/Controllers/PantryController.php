<?php

namespace App\Http\Controllers;

use App\Models\PantryItem;
use App\Models\ShoppingItem;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class PantryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $items = PantryItem::where('user_id', $user->id)->get();
        return response()->json(['status' => 'success', 'data' => $items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string'
        ]);
        $user = $request->user();
        
        $existing = PantryItem::where('user_id', $user->id)
            ->where('item_name', $request->item_name)
            ->first();
            
        if ($existing) {
            return response()->json(['status' => 'error', 'message' => 'Item already exists'], 409);
        }

        // ✅ تصحيح: البحث باستخدام name_en بدلاً من name
        $ingredient = Ingredient::where('name_en', $request->item_name)
            ->orWhere('name_ar', $request->item_name)
            ->first();

        $item = PantryItem::create([
            'user_id' => $user->id,
            'item_name' => strtolower($request->item_name),
            'ingredient_id' => $ingredient ? $ingredient->id : ($request->ingredient_id ?? null)
        ]);

        return response()->json(['status' => 'success', 'data' => $item]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $item = PantryItem::where('user_id', $user->id)->where('id', $id)->first();

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Item not found'], 404);
        }

        $item->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }

    public function indexWithLang(Request $request)
    {
        $lang = app()->getLocale();
        if($request->has('lang')) {
            $lang = $request->query('lang');
        }
        
        $user = $request->user();

        $items = PantryItem::with('ingredient')->where('user_id', $user->id)->get();

        $data = $items->map(function ($item) use ($lang) {
            
            $translatedName = null;
            if ($item->ingredient) {
                // ✅ تصحيح: استخدام name_en
                $translatedName = $lang === 'ar' ? $item->ingredient->name_ar : $item->ingredient->name_en;
            }

            return [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'display_name' => $translatedName ?? $item->item_name
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function destroyAndSync(Request $request, $id)
    {
        $user = $request->user();

        $item = PantryItem::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found'
            ], 404);
        }

        ShoppingItem::where('user_id', $user->id)
            ->where('item_name', $item->item_name)
            ->update(['is_checked' => false]);

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted and synced with shopping list'
        ]);
    }
}
