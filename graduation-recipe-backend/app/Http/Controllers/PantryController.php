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

        $item = PantryItem::create([
            'user_id' => 1,
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
