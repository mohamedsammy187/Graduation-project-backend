<?php

namespace App\Http\Controllers;

use App\Models\PantryItem;
use Illuminate\Http\Request;

class PantryController extends Controller
{
    public function index()
    {
        $items = PantryItem::where('user_id', 1)->get(); // static user
        return response()->json(['status'=>'success','data'=>$items]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string'
        ]);
        $userId = 1; // static user
        $existing = PantryItem::where('user_id', $userId)->where('item_name',$request->item_name)->first();
        if ($existing) {
            return response()->json(['status'=>'error','message'=>'Item already exists'], 409);
        }

        $item = PantryItem::create([
            'user_id' => $userId,
            'item_name' => $request->item_name,
            'ingredient_id' => $request->ingredient_id
        ]);

        return response()->json(['status'=>'success','data'=>$item]);
    }

    public function destroy($id)
    {
        $item = PantryItem::where('user_id',1)->where('id', $id)->first();

        if (!$item) {
            return response()->json(['status'=>'error','message'=>'Item not found'], 404);
        }

        $item->delete();
        return response()->json(['status'=>'success','message'=>'Deleted']);
    }
}
