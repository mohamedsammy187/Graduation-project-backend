<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Step;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecipeAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with(['categoryInfo', 'user']);

        $query->when($request->title, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('title_en', 'like', '%' . $request->title . '%')
                    ->orWhere('title_ar', 'like', '%' . $request->title . '%');
            });
        });

        $query->when($request->id, function ($q) use ($request) {
            $q->where('id', $request->id);
        });

        $query->when($request->category_id, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('category_id', $request->category_id);

                $category = Category::find($request->category_id);
                if ($category) {
                    $sub->orWhere('category', 'like', '%' . $category->name_en . '%')
                        ->orWhere('category', 'like', '%' . $category->name_ar . '%');
                }
            });
        });

        $query->when($request->time, function ($q) use ($request) {
            $q->where('time', $request->time);
        });

        $query->when($request->calories, function ($q) use ($request) {
            $q->where('calories', $request->calories);
        });

        return $query->latest()->paginate(10);
    }

    public function getFilterOptions()
    {
        return response()->json([
            'categories' => Category::select('id', 'name_en', 'name_ar')->get(),
            'times' => Recipe::select('time')->distinct()->whereNotNull('time')->orderBy('time')->pluck('time'),
            'calories' => Recipe::select('calories')->distinct()->whereNotNull('calories')->orderBy('calories')->pluck('calories'),
        ]);
    }

    public function show($id)
    {
        return Recipe::with(['ingredients', 'steps', 'user', 'categoryInfo'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'category_id' => 'required',
            'meal_type' => 'nullable|string',
            'cuisine' => 'nullable|string',
            'time' => 'required|integer',
            'difficulty' => 'required|string',
            'calories' => 'nullable|integer',
            'temperature' => 'nullable|string',
            'nutrition' => 'nullable|array',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required',
            'steps' => 'required|array|min:1',
            'steps.*.instruction_en' => 'required|string',
            'steps.*.instruction_ar' => 'required|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $imagePath = $file->store('recipes', 'public');
            } catch (\Exception $e) {
                return response()->json(['message' => 'UPLOAD ERROR', 'details' => $e->getMessage()], 500);
            }
        }

        try {
            $recipe = DB::transaction(function () use ($request, $validated, $imagePath) {
                // Prepare Legacy Steps JSON
                $legacySteps = [];
                foreach ($request->steps as $step) {
                    $legacySteps[] = ['instruction_en' => $step['instruction_en'], 'instruction_ar' => $step['instruction_ar']];
                }

                // Prepare Legacy Ingredients JSON
                $ingredientIds = collect($request->ingredients)->pluck('id');
                $dbIngredients = Ingredient::whereIn('id', $ingredientIds)->get()->keyBy('id');
                $legacyIngredients = [];
                foreach ($request->ingredients as $ing) {
                    $dbIng = $dbIngredients->get($ing['id']);
                    if ($dbIng) {
                        $legacyIngredients[] = [
                            'id' => $ing['id'],
                            'name_en' => $dbIng->name_en,
                            'name_ar' => $dbIng->name_ar,
                            'quantity' => $ing['quantity'],
                            'unit' => $ing['unit'] ?? ''
                        ];
                    }
                }

                $recipe = Recipe::create(array_merge($validated, [
                    'image' => $imagePath,
                    'user_id' => auth()->id() ?? 1,
                    'nutrition' => isset($validated['nutrition']) ? json_encode($validated['nutrition']) : null,
                    'steps' => json_encode($legacySteps, JSON_UNESCAPED_UNICODE),
                    'ingredients' => json_encode($legacyIngredients, JSON_UNESCAPED_UNICODE),
                ]));

                foreach ($request->ingredients as $ing) {
                    $recipe->ingredients()->attach($ing['id'], ['quantity' => $ing['quantity'], 'unit' => $ing['unit'] ?? '']);
                }

                foreach ($request->steps as $index => $stepData) {
                    Step::create([
                        'recipe_id' => $recipe->id,
                        'step_number' => $index + 1,
                        'instruction_en' => $stepData['instruction_en'],
                        'instruction_ar' => $stepData['instruction_ar'],
                    ]);
                }

                return $recipe;
            });

            return response()->json(['status' => 'success', 'data' => $recipe], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        $validated = $request->validate([
            'title_en' => 'sometimes|string',
            'title_ar' => 'sometimes|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'meal_type' => 'nullable|string',
            'cuisine' => 'nullable|string',
            'time' => 'sometimes|integer',
            'difficulty' => 'sometimes|string',
            'calories' => 'nullable|integer',
            'temperature' => 'nullable|string',
            'nutrition' => 'nullable|array',
            'ingredients' => 'sometimes|array',
            'steps' => 'sometimes|array',
        ]);

        try {
            $recipe = DB::transaction(function () use ($request, $recipe, $validated) {
                if ($request->hasFile('image')) {
                    if ($recipe->image && Storage::disk('public')->exists($recipe->image)) {
                        Storage::disk('public')->delete($recipe->image);
                    }
                    $validated['image'] = $request->file('image')->store('recipes', 'public');
                }

                if ($request->has('ingredients')) {
                    $syncData = [];
                    foreach ($request->ingredients as $ing) {
                        $syncData[$ing['id']] = [
                            'quantity' => $ing['quantity'],
                            'unit' => $ing['unit'] ?? ''
                        ];
                    }
                    $recipe->ingredients()->sync($syncData);

                    // Update Legacy JSON Ingredients
                    $ingredientIds = collect($request->ingredients)->pluck('id');
                    $dbIngredients = Ingredient::whereIn('id', $ingredientIds)->get()->keyBy('id');
                    $legacyIngredients = [];
                    foreach ($request->ingredients as $ing) {
                        $dbIng = $dbIngredients->get($ing['id']);
                        if ($dbIng) {
                            $legacyIngredients[] = [
                                'id' => $ing['id'],
                                'name_en' => $dbIng->name_en,
                                'name_ar' => $dbIng->name_ar,
                                'quantity' => $ing['quantity'],
                                'unit' => $ing['unit'] ?? ''
                            ];
                        }
                    }
                    $validated['ingredients'] = json_encode($legacyIngredients, JSON_UNESCAPED_UNICODE);
                }

                if ($request->has('steps')) {
                    $recipe->steps()->delete();
                    $legacySteps = [];
                    foreach ($request->steps as $index => $stepData) {
                        Step::create([
                            'recipe_id' => $recipe->id,
                            'step_number' => $index + 1,
                            'instruction_en' => $stepData['instruction_en'],
                            'instruction_ar' => $stepData['instruction_ar'],
                        ]);
                        $legacySteps[] = [
                            'instruction_en' => $stepData['instruction_en'],
                            'instruction_ar' => $stepData['instruction_ar']
                        ];
                    }
                    $validated['steps'] = json_encode($legacySteps, JSON_UNESCAPED_UNICODE);
                }

                $recipe->update($validated);
                return $recipe;
            });

            return response()->json(['status' => 'success', 'data' => $recipe]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        if ($recipe->image && Storage::disk('public')->exists($recipe->image)) {
            Storage::disk('public')->delete($recipe->image);
        }
        $recipe->delete();
        return response()->json(['message' => 'Recipe deleted successfully']);
    }
}