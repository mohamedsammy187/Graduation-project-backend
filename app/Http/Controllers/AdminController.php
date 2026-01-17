<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeAdminController extends Controller
{
    public function index()
    {
        return Recipe::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        $recipe = Recipe::create($validated);

        return response()->json($recipe, 201);
    }

    public function show($id)
    {
        return Recipe::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        $validated = $request->validate([
            'title_en' => 'sometimes|string',
            'title_ar' => 'sometimes|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        $recipe->update($validated);

        return response()->json($recipe);
    }

    public function destroy($id)
    {
        Recipe::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Recipe deleted'
        ]);
    }
}
