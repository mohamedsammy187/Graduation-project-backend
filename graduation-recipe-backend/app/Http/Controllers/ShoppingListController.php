<?php

namespace App\Http\Controllers;

use App\Models\ShoppingItem;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    private $userId = 1;

    // GET /shopping-list
    public function index()
    {
        return ShoppingItem::where('user_id', $this->userId)->get();
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
                'user_id' => $this->userId,
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
        $item = ShoppingItem::where('user_id', $this->userId)
            ->where('id', $id)
            ->firstOrFail();

        $item->update([
            'is_checked' => $request->is_checked
        ]);

        return response()->json(['status' => 'updated']);
    }

    // DELETE /shopping-list/{id}
    public function destroy($id)
    {
        ShoppingItem::where('user_id', $this->userId)
            ->where('id', $id)
            ->delete();

        return response()->json(['status' => 'deleted']);
    }
}
