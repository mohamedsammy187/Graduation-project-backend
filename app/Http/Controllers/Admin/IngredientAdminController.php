<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IngredientAdminController extends Controller
{
    // ... index, show تظل كما هي بدون تغيير ...

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name_en' => 'required|string|unique:ingredients,name_en',
        'name_ar' => 'required|string|unique:ingredients,name_ar',
    ], [
        'name_en.unique' => 'ERR_UNIQUE_EN',
        'name_ar.unique' => 'ERR_UNIQUE_AR',
    ]);

    if ($validator->fails()) {
        $errors = $validator->errors();
        // لو الاثنين مكررين نبعت كود خاص
        if ($errors->has('name_en') && $errors->has('name_ar')) {
            return response()->json(['message' => 'ERR_UNIQUE_BOTH'], 422);
        }
        // نبعت أول خطأ حصل (سواء عربي أو إنجليزي)
        return response()->json(['message' => $errors->first()], 422);
    }

    $ingredient = Ingredient::create($request->all());
    return response()->json(['status' => 'success', 'data' => $ingredient]);
}

    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_en' => [
                'sometimes', 'string',
                Rule::unique('ingredients')->ignore($ingredient->id),
            ],
            'name_ar' => [
                'sometimes', 'string',
                Rule::unique('ingredients')->ignore($ingredient->id),
            ],
            'category' => 'nullable|string',
        ], [
            'name_en.unique' => 'EXISTS_EN',
            'name_ar.unique' => 'EXISTS_AR',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->has('name_en') && $errors->has('name_ar')) {
                return response()->json(['message' => 'EXISTS_BOTH'], 422);
            }
            return response()->json(['message' => $errors->first()], 422);
        }

        $ingredient->update($request->all());
        return response()->json(['status' => 'updated', 'data' => $ingredient]);
    }

    // ... index, show, destroy تظل كما هي ...
    public function index(Request $request)
    {
        $query = Ingredient::query();
        if ($request->filled('id')) $query->where('id', $request->id);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_en', 'like', "%$search%")->orWhere('name_ar', 'like', "%$search%");
            });
        }
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->has('all')) return $query->orderBy('name_en')->get();
        return $query->orderBy('name_en')->paginate(20);
    }

    public function destroy($id)
    {
        Ingredient::findOrFail($id)->delete();
        return response()->json(['status' => 'deleted']);
    }
}