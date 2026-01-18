<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use App\Models\PantryItem;
use App\Models\ShoppingItem;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function test()
    {
        return response()->json(['message' => 'API is working']);
    }


    private function getLocalizedRecipe(Recipe $recipe)
    {
        // $lang = app()->getLocale();
        $lang = request()->get('lang', 'en');
        $steps = json_decode($recipe->steps, true);

        return [
            'id' => $recipe->id,
            'title' => $lang === 'ar' ? $recipe->title_ar : ($recipe->title_en ?? $recipe->title),
            'slug' => $recipe->slug,
            'time' => $recipe->time,
            'difficulty' => $recipe->difficulty,
            'calories' => $recipe->calories,
            'temperature' => $recipe->temperature,
            'image' => $recipe->image,
            'servings' => $recipe->servings,
            'cuisine' => $recipe->cuisine,
            'description' => $recipe->description,

            'ingredients' => $recipe->ingredients->map(fn($i) => [
                'id' => $i->id,
                'name' => $lang === 'ar' ? $i->name_ar : $i->name_en, // ✅ تعديل للعرض
                'quantity' => $i->pivot->quantity,
                'unit' => $i->pivot->unit,
                'display_text' => $i->pivot->display_text,
                'is_optional' => $i->pivot->is_optional,
            ]),

            'steps' => $steps[$lang] ?? $steps['en'],
        ];
    }


    public function index()
    {
        $recipes = Recipe::with('ingredients')->paginate(9);

        return response()->json([
            'data' => $recipes->through(
                fn($recipe) =>
                $this->getLocalizedRecipe($recipe)
            ),
            'meta' => [
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'per_page' => $recipes->perPage(),
                'total' => $recipes->total(),
            ],
        ]);
    }




    public function show($id)
    {
        $recipe = Recipe::with('ingredients')->findOrFail($id);

        return response()->json(
            $this->getLocalizedRecipe($recipe)
        );
    }






    public function showrecipe($slug)
    {
        $recipe = Recipe::with('ingredients')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(
            $this->getLocalizedRecipe($recipe)
        );
    }



    public function store(Request $request)
    {
        $recipe = Recipe::create($request->all());
        return response()->json($recipe, 201);
    }





    public function matchPantry(Request $request)
    {
        $user = $request->user();
        $lang = $request->get('lang', 'en'); // ✅ 1

        // 🧺 1. Get pantry (Standardized to English)
        $pantry = PantryItem::with('ingredient')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                // ✅ نستخدم الاسم الانجليزي للمكون المربوط، أو الاسم المسجل
                $name = $item->ingredient ? $item->ingredient->name_en : $item->item_name;
                return strtolower(trim($name));
            });

        if ($pantry->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'count' => 0,
                'data' => [],
                'message' => 'Pantry is empty'
            ]);
        }

        // 🔧 optional flag from query
        // /recipes/match-pantry?allow_missing_one=true
        $allowMissingOne = filter_var(
            $request->query('allow_missing_one', false),
            FILTER_VALIDATE_BOOLEAN
        );

        // 🍽 2. Load recipes
        $recipes = Recipe::with('ingredients');
        //  filters for matching
        if ($request->query('keyword')) {
            $keyword = $request->query('keyword');
            $recipes = $recipes->where('title', 'like', "%{$keyword}%");
        }
        if ($request->query('difficulty')) {
            $difficulty = $request->query('difficulty');
            $recipes = $recipes->where('difficulty', $difficulty);
        }
        if ($request->query('max_time')) {
            $maxTime = $request->query('max_time');
            $recipes = $recipes->where('time', '<=', $maxTime);
        }
        if ($request->query('category')) {
            $category = $request->query('category');
            $recipes = $recipes->where('category', $category);
        }
        if ($request->query('meal_type')) {
            $mealType = $request->query('meal_type');
            $recipes = $recipes->where('meal_type', $mealType);
        }
        if ($request->query('temperature')) {
            $mealType = $request->query('temperature');
            $recipes = $recipes->where('temperature', $mealType);
        }

        $recipes = $recipes->get();
        // 🧠 Match Logic
        $matchedRecipes = $recipes
            ->map(function ($recipe) use ($pantry, $lang) {

                // ✅ التعديل: فلترة الكائنات للحفاظ على الترجمة
                $missingObjects = $recipe->ingredients->filter(function ($ingredient) use ($pantry) {
                    return !$pantry->contains(strtolower($ingredient->name_en));
                });

                $matchCount = $recipe->ingredients->count() - $missingObjects->count();

                return [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'slug' => $recipe->slug,
                    'image' => $recipe->image,
                    'difficulty' => $recipe->difficulty,
                    'time' => $recipe->time,
                    'category' => $recipe->category,
                    'calories' => $recipe->calories,
                    'steps' => json_decode($recipe->steps, true),

                    'match_count' => $matchCount,
                    'matched_ingredients' => [],

                    'missing_count' => $missingObjects->count(),

                    // ✅ إرجاع النواقص باللغة المطلوبة
                    'missing_ingredients' => $missingObjects->map(
                        fn($i) =>
                        $lang === 'ar' ? $i->name_ar : $i->name_en
                    )->values(),
                ];
            })

            // 🧠 3. Filter logic
            ->filter(function ($recipe) use ($allowMissingOne) {

                if ($recipe['match_count'] === 0) {
                    return false;
                }

                if ($recipe['missing_count'] === 0) {
                    return true;
                }

                return $allowMissingOne && $recipe['missing_count'] === 1;
            })

            // 📊 4. Sort
            ->sortBy([
                ['missing_count', 'asc'],
                ['match_count', 'desc'],
            ])
            ->values();

        return response()->json([
            'status' => 'success',
            'count' => $matchedRecipes->count(),
            'data' => $matchedRecipes
        ]);
    }




    public function surpriseMe(Request $request)
    {
        $user = $request->user();
        $lang = $request->get('lang', 'en'); // ✅ 2

        // 1️⃣ Get pantry (Standardized to English)
        $pantry = PantryItem::with('ingredient')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                // ✅ توحيد الاسم للانجليزية
                $name = $item->ingredient ? $item->ingredient->name_en : $item->item_name;
                return strtolower(trim($name));
            });

        if ($pantry->isEmpty()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Your pantry is empty. Add ingredients first.'
            ], 400);
        }

        // 2️⃣ Load recipes
        $recipes = Recipe::with('ingredients')->get();

        $matched = $recipes
            ->filter(fn($r) => $r->id !== $user->last_surprise_recipe_id)
            ->map(function ($recipe) use ($pantry, $lang) {

                // ✅ نفس المنطق
                $missingObjects = $recipe->ingredients->filter(function ($ingredient) use ($pantry) {
                    return !$pantry->contains(strtolower($ingredient->name_en));
                });

                $matchCount = $recipe->ingredients->count() - $missingObjects->count();

                return [
                    'recipe' => $recipe,
                    'matched_count' => $matchCount,
                    'missing_count' => $missingObjects->count(),
                    'missing_objects' => $missingObjects
                ];
            })

            // ✅ must match at least 1 ingredient
            ->filter(fn($r) => $r['matched_count'] > 0)

            // 🎲 allow missing one
            ->filter(fn($r) => $r['missing_count'] <= 1)
            ->values();

        if ($matched->isEmpty()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No suitable recipe found with your pantry.'
            ]);
        }

        // 3️⃣ Pick random recipe
        $selected = $matched->random();
        $recipe = $selected['recipe'];

        // 4️⃣ Save last suggested recipe
        $user->update([
            'last_surprise_recipe_id' => $recipe->id
        ]);

        // 5️⃣ Response
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
                'image' => $recipe->image,
                'time' => $recipe->time,
                'difficulty' => $recipe->difficulty,
                'calories' => $recipe->calories,

                'ingredients' => $recipe->ingredients->map(fn($i) => [
                    'id' => $i->id,
                    'name' => $lang === 'ar' ? $i->name_ar : $i->name_en // ✅ عرض الاسم المترجم
                ]),

                'missing_ingredients' => $selected['missing_objects']->map(
                    fn($i) =>
                    $lang === 'ar' ? $i->name_ar : $i->name_en
                )->values(),

                // ✅ deployment-safe link
                'link' => rtrim(config('app.url'), '/') . "/api/recipes/slug/{$recipe->slug}",
            ]
        ]);
    }


    public function topLoved(Request $request)
    {
        // نستقبل اللغة من الرابط
        $lang = $request->get('lang', 'en');

        // 1. نجيب الـ IDs لأعلى 3 وصفات محبوبة
        $topRecipeIds = DB::table('favorites')
            ->select('recipe_id', DB::raw('count(*) as loves_count'))
            ->groupBy('recipe_id')
            ->orderByDesc('loves_count')
            ->limit(3)
            ->pluck('recipe_id');

        // 2. نجيب تفاصيل الوصفات دي كاملة (عشان المكونات تترجم)
        $recipes = Recipe::with('ingredients')
            ->whereIn('id', $topRecipeIds)
            ->get()
            ->map(function ($recipe) use ($lang) {
                // استخدام دالة الترجمة الموحدة
                return $this->getLocalizedRecipe($recipe);
            });

        return response()->json([
            'status' => 'success',
            'data' => $recipes
        ]);
    }
}
