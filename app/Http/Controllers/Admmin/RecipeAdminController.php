<?php

namespace App\Http\Controllers\Admmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recipe;

class RecipeAdminController extends Controller
{
    public function index()
    {
        $recipes = Recipe::all();
        return response()->json($recipes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
        ]);


        $recipe = Recipe::create($validated);
        return response()->json($recipe, 201);
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
        return response()->json(['message' => 'Recipe deleted successfully.']);
    }
}
