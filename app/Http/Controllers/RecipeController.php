<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\PantryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function test()
    {
        return response()->json(['message' => 'API is working']);
    }

    /**
     * Helper to format localized response
     */
    private function getLocalizedRecipe(Recipe $recipe)
    {
        $lang = request()->get('lang', 'en');

        // 1. Ingredients handling
        if ($recipe->relationLoaded('ingredients') && $recipe->getRelation('ingredients')->isNotEmpty()) {
            $ingredients = $recipe->getRelation('ingredients')->map(function ($i) use ($lang) {
                return [
                    'id' => $i->id,
                    'name' => $lang === 'ar' ? ($i->name_ar ?? $i->name_en) : ($i->name_en ?? $i->name_ar),
                    'quantity' => $i->pivot->quantity ?? '',
                    'unit' => $i->pivot->unit ?? '',
                    'display_text' => $i->pivot->display_text ?? '',
                    'is_optional' => $i->pivot->is_optional ?? 0,
                ];
            });
        } else {
            $rawJson = $recipe->getAttribute('ingredients');
            $jsonIngs = !empty($rawJson) ? json_decode($rawJson, true) : [];
            $ingredients = collect(is_array($jsonIngs) ? $jsonIngs : [])->map(function ($item) use ($lang) {
                if (is_string($item))
                    return ['name' => $item, 'id' => null, 'quantity' => '', 'unit' => '', 'is_optional' => 0];
                $name = $lang === 'ar' ? ($item['name_ar'] ?? $item['item_name'] ?? '') : ($item['name_en'] ?? $item['item_name'] ?? '');
                return [
                    'id' => $item['id'] ?? null,
                    'name' => $name,
                    'quantity' => $item['quantity'] ?? '',
                    'unit' => $item['unit'] ?? '',
                    'display_text' => null,
                    'is_optional' => 0
                ];
            });
        }

        // 2. Steps handling
        if ($recipe->relationLoaded('steps') && $recipe->getRelation('steps')->isNotEmpty()) {
            $steps = $recipe->getRelation('steps')->map(function ($step) use ($lang) {
                return $lang === 'ar' ? ($step->instruction_ar ?? $step->instruction_en) : ($step->instruction_en ?? $step->instruction_ar);
            });
        } else {
            $rawSteps = $recipe->getAttribute('steps');
            $jsonSteps = !empty($rawSteps) ? json_decode($rawSteps, true) : [];
            $steps = collect($jsonSteps[$lang] ?? $jsonSteps['en'] ?? $jsonSteps ?? []);
        }

        return [
            'id' => $recipe->id,
            'title' => $lang === 'ar' ? ($recipe->title_ar ?? $recipe->title_en) : ($recipe->title_en ?? $recipe->title_ar),
            'description' => $lang === 'ar' ? ($recipe->description_ar ?? $recipe->description_en) : ($recipe->description_en ?? $recipe->description_ar),
            'slug' => $recipe->slug,
            'time' => intval($recipe->time) . ' min',
            'difficulty' => $recipe->difficulty,
            'calories' => $recipe->calories,
            'temperature' => $recipe->temperature ? ucfirst(strtolower($recipe->temperature)) : '',
            'image' => $recipe->image,
            'servings' => $recipe->servings,
            'cuisine' => $recipe->cuisine,
            'category' => $recipe->category, // يعتمد على الـ Accessor في الموديل
            'ingredients' => $ingredients,
            'steps' => $steps,
        ];
    }

    /**
     * Fixed Search Function for Explore Page
     */
    public function search(Request $request)
    {
        $query = Recipe::with(['ingredients', 'steps', 'categoryInfo', 'user']);

        // 1. فلترة القسم (ID أو نص)
        if ($request->filled('category')) {
            $cat = $request->category;
            $query->where(function ($q) use ($cat) {
                if (is_numeric($cat)) {
                    $q->where('category_id', $cat);
                } else {
                    $catLower = strtolower($cat);
                    $q->whereHas('categoryInfo', function ($sub) use ($catLower) {
                        $sub->where(DB::raw('LOWER(name_en)'), $catLower)
                            ->orWhere(DB::raw('LOWER(name_ar)'), $catLower);
                    })->orWhere(DB::raw('LOWER(category)'), 'like', "%$catLower%");
                }
            });
        }

        // 2. فلترة المكونات (تم التحديث لدعم البحث المتعدد كما في الكنترولر القديم)
        if ($request->filled('ingredients')) {
            $ingredients = is_string($request->ingredients)
                ? explode(',', $request->ingredients)
                : $request->ingredients;

            $query->whereHas('ingredients', function ($q) use ($ingredients) {
                $q->where(function ($sub) use ($ingredients) {
                    $sub->whereIn(DB::raw('LOWER(name_en)'), array_map('strtolower', $ingredients))
                        ->orWhereIn('name_ar', $ingredients);
                });
            });
        }

        // 3. فلترة نوع الوجبة
        if ($request->filled('meal_type')) {
            $query->where(DB::raw('LOWER(meal_type)'), strtolower($request->meal_type));
        }

        // 4. فلترة درجة الحرارة
        if ($request->filled('temperature')) {
            $query->where(DB::raw('LOWER(temperature)'), strtolower($request->temperature));
        }

        // 5. البادجينيشن (Pagination)
        $recipes = $query->latest()->paginate(9);

        // تحويل البيانات للغة المطلوبة مع الحفاظ على هيكل الـ Pagination
        return $recipes->through(fn($recipe) => $this->getLocalizedRecipe($recipe));
    }

    public function matchPantry(Request $request)
    {
        $user = $request->user();
        $lang = $request->get('lang', 'en');

        $pantry = PantryItem::with('ingredient')
            ->where('user_id', $user->id)
            ->get()
            ->map(fn($item) => strtolower(trim($item->ingredient ? $item->ingredient->name_en : $item->item_name)));

        if ($pantry->isEmpty()) {
            return response()->json(['status' => 'success', 'count' => 0, 'data' => [], 'message' => 'Pantry is empty']);
        }

        $allowMissingOne = filter_var($request->query('allow_missing_one', false), FILTER_VALIDATE_BOOLEAN);

        $query = Recipe::with(['ingredients', 'steps', 'categoryInfo']);

        if ($request->filled('category')) {
            $cat = strtolower($request->category);
            $query->where(function ($q) use ($cat) {
                $q->whereHas('categoryInfo', function ($sub) use ($cat) {
                    $sub->where(DB::raw('LOWER(name_en)'), $cat)->orWhere(DB::raw('LOWER(name_ar)'), $cat);
                })->orWhere(DB::raw('LOWER(category)'), $cat);
            });
        }

        if ($request->filled('meal_type')) {
            $query->where(DB::raw('LOWER(meal_type)'), strtolower($request->meal_type));
        }

        if ($request->filled('temperature')) {
            $query->where(DB::raw('LOWER(temperature)'), strtolower($request->temperature));
        }

        if ($request->filled('keyword')) {
            $query->where(fn($q) => $q->where('title_en', 'like', "%{$request->keyword}%")->orWhere('title_ar', 'like', "%{$request->keyword}%"));
        }

        $matchedRecipes = $query->get()
            ->map(function ($recipe) use ($pantry, $lang) {
                if (!$recipe->relationLoaded('ingredients') || $recipe->getRelation('ingredients')->isEmpty())
                    return null;

                $recipeIngredients = $recipe->getRelation('ingredients');
                $missingObjects = $recipeIngredients->filter(fn($ing) => !$pantry->contains(strtolower($ing->name_en)));
                $matchCount = $recipeIngredients->count() - $missingObjects->count();

                $stepsData = ($recipe->relationLoaded('steps') && $recipe->getRelation('steps')->isNotEmpty())
                    ? $recipe->getRelation('steps')->map(fn($s) => $lang == 'ar' ? $s->instruction_ar : $s->instruction_en)
                    : [];

                return [
                    'id' => $recipe->id,
                    'title' => $lang === 'ar' ? ($recipe->title_ar ?? $recipe->title_en) : ($recipe->title_en ?? $recipe->title_ar),
                    'slug' => $recipe->slug,
                    'image' => $recipe->image,
                    'difficulty' => $recipe->difficulty,
                    'time' => intval($recipe->time) . ' min',
                    'category' => $recipe->category,
                    'calories' => $recipe->calories,
                    'temperature' => $recipe->temperature ? ucfirst(strtolower($recipe->temperature)) : '',
                    'steps' => $stepsData,
                    'match_count' => $matchCount,
                    'missing_count' => $missingObjects->count(),
                    'missing_ingredients' => $missingObjects->map(function ($i) use ($lang) {
                        return [
                            'id' => $i->id,
                            'name' => ($lang === 'ar' ? ($i->pivot->ingredient_name_ar ?? $i->name_ar ?? $i->name_en) : ($i->name_en ?? $i->name))
                        ];
                    })->values(),
                ];
            })
            ->filter()
            ->filter(fn($recipe) => $recipe['match_count'] > 0 && ($recipe['missing_count'] === 0 || ($allowMissingOne && $recipe['missing_count'] === 1)))
            ->sortBy([['missing_count', 'asc'], ['match_count', 'desc']])
            ->values();

        return response()->json(['status' => 'success', 'count' => $matchedRecipes->count(), 'data' => $matchedRecipes]);
    }

    public function index()
    {
        $recipes = Recipe::with(['ingredients', 'steps', 'categoryInfo'])->paginate(9);
        return response()->json([
            'status' => 'success',
            'data' => $recipes->through(fn($recipe) => $this->getLocalizedRecipe($recipe)),
            'meta' => [
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'total' => $recipes->total(),
            ],
        ]);
    }

    public function show($id)
    {
        $recipe = Recipe::with(['ingredients', 'steps', 'categoryInfo'])->findOrFail($id);
        return response()->json($this->getLocalizedRecipe($recipe));
    }

    public function showrecipe($slug)
    {
        $recipe = Recipe::with(['ingredients', 'steps', 'categoryInfo'])->where('slug', $slug)->firstOrFail();
        return response()->json($this->getLocalizedRecipe($recipe));
    }

    public function surpriseMe(Request $request)
    {
        try {
            $user = $request->user();
            $lang = $request->get('lang', 'en');

            // 1. جلب مكونات المطبخ بنفس منطق matchPantry
            $pantry = PantryItem::with('ingredient')
                ->where('user_id', $user->id)
                ->get()
                ->map(fn($item) => strtolower(trim($item->ingredient ? $item->ingredient->name_en : $item->item_name)));

            if ($pantry->isEmpty()) {
                return response()->json(['status' => 'fail', 'message' => 'Pantry empty'], 400);
            }

            // 2. جلب الوصفات مع العلاقات الضرورية
            $recipes = Recipe::with(['ingredients', 'steps', 'categoryInfo'])->get();

            // 3. تطبيق منطق الماتشنج
            $matchedRecipes = $recipes->map(function ($recipe) use ($pantry, $user) {
                if ($recipe->id === $user->last_surprise_recipe_id)
                    return null;

                if (!$recipe->relationLoaded('ingredients') || $recipe->getRelation('ingredients')->isEmpty()) {
                    return null;
                }

                $recipeIngredients = $recipe->getRelation('ingredients');
                $missingObjects = $recipeIngredients->filter(fn($ing) => !$pantry->contains(strtolower($ing->name_en)));
                $matchCount = $recipeIngredients->count() - $missingObjects->count();

                return [
                    'recipe' => $recipe,
                    'match_count' => $matchCount,
                    'missing_count' => $missingObjects->count(),
                    'missing_objects' => $missingObjects
                ];
            })
                ->filter()
                ->filter(fn($item) => $item['match_count'] > 0 && $item['missing_count'] <= 1)
                ->values();

            if ($matchedRecipes->isEmpty()) {
                return response()->json(['status' => 'fail', 'message' => 'No recipe found matching your pantry']);
            }

            $selected = $matchedRecipes->random();
            $recipe = $selected['recipe'];

            $user->update(['last_surprise_recipe_id' => $recipe->id]);

            $localized = $this->getLocalizedRecipe($recipe);

            // --- الحل الجذري: نرسل الـ ID مع الاسم لضمان عمل المقارنة عند تحويل اللغة في الفرونت ---
            $localized['missing_ingredients'] = $selected['missing_objects']->map(function ($i) use ($lang) {
                return [
                    'id' => $i->id,
                    'name' => ($lang === 'ar' ? ($i->pivot->ingredient_name_ar ?? $i->name_ar ?? $i->name_en) : ($i->name_en ?? $i->name))
                ];
            })->values();

            $localized['link'] = rtrim(config('app.url'), '/') . "/api/recipes/slug/{$recipe->slug}";

            return response()->json(['status' => 'success', 'data' => $localized]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'details' => $e->getMessage()], 500);
        }
    }
    public function topLoved(Request $request)
    {
        $lang = $request->get('lang', 'en');
        $topRecipeIds = DB::table('favorites')->select('recipe_id', DB::raw('count(*) as loves_count'))->groupBy('recipe_id')->orderByDesc('loves_count')->limit(3)->pluck('recipe_id');
        $recipes = Recipe::with(['ingredients', 'steps', 'categoryInfo'])->whereIn('id', $topRecipeIds)->get()->map(fn($recipe) => $this->getLocalizedRecipe($recipe));
        return response()->json(['status' => 'success', 'data' => $recipes]);
    }
}