<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientAdminController extends Controller
{
    public function index()
    {
        return Ingredient::all();
    }

    public function show($id)
    {
        return Ingredient::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string',
            'name_ar' => 'required|string',
            'category' => 'nullable|string',
            'image' => 'nullable|string'
        ]);

        $ingredient = Ingredient::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $ingredient
        ]);
    }

    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);

        $request->validate([
            'name_en' => 'sometimes|string',
            'name_ar' => 'sometimes|string',
            'category' => 'nullable|string',
            'image' => 'nullable|string'
        ]);

        $ingredient->update($request->all());

        return response()->json([
            'status' => 'updated',
            'data' => $ingredient
        ]);
    }

    public function destroy($id)
    {
        Ingredient::findOrFail($id)->delete();

        return response()->json([
            'status' => 'deleted'
        ]);
    }
}
