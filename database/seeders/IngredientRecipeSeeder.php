<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;

class IngredientRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = Ingredient::all()->keyBy('name_en');
        $recipes = Recipe::all();

        // 🔥 Smart Recipe Ingredient Map (with quantities)
        $map = [

            'Pancakes' => [
                ['name_en' => 'egg', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'بيض'],
                ['name_en' => 'flour', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'دقيق'],
                ['name_en' => 'milk', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'لبن'],
                ['name_en' => 'sugar', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'سكر'],
            ],

            'Omelette' => [
                ['name_en' => 'egg', 'qty' => 3, 'unit' => 'pcs', 'name_ar' => 'بيض'],
                ['name_en' => 'butter', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'زبدة'],
                ['name_en' => 'salt', 'qty' => 0.5, 'unit' => 'tsp', 'name_ar' => 'ملح'],
                ['name_en' => 'pepper', 'qty' => 0.25, 'unit' => 'tsp', 'name_ar' => 'فلفل'],
            ],

            'French Toast' => [
                ['name_en' => 'bread', 'qty' => 4, 'unit' => 'slices', 'name_ar' => 'عيش'],
                ['name_en' => 'egg', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'بيض'],
                ['name_en' => 'milk', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'لبن'],
                ['name_en' => 'sugar', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'سكر'],
            ],

            'Oats' => [
                ['name_en' => 'oats', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'شوفان'],
                ['name_en' => 'milk', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'لبن'],
                ['name_en' => 'honey', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'عسل'],
            ],

            'Grilled Chicken' => [
                ['name_en' => 'chicken', 'qty' => 500, 'unit' => 'g', 'name_ar' => 'فراخ'],
                ['name_en' => 'olive oil', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'زيت زيتون'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
                ['name_en' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'name_ar' => 'فلفل'],
                ['name_en' => 'herbs', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'أعشاب'],
            ],

            'Pasta' => [
                ['name_en' => 'pasta', 'qty' => 200, 'unit' => 'g', 'name_ar' => 'مكرونة'],
                ['name_en' => 'chicken', 'qty' => 200, 'unit' => 'g', 'name_ar' => 'فراخ'],
                ['name_en' => 'sauce', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'صلصة'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
                ['name_en' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'name_ar' => 'فلفل'],
            ],

            'Burger' => [
                ['name_en' => 'beef', 'qty' => 200, 'unit' => 'g', 'name_ar' => 'لحم'],
                ['name_en' => 'bun', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'خبز'],
                ['name_en' => 'cheese', 'qty' => 2, 'unit' => 'slices', 'name_ar' => 'جبنة'],
                ['name_en' => 'lettuce', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'خس'],
                ['name_en' => 'tomato', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'طماطم'],
            ],

            'Lemon Juice' => [
                ['name_en' => 'lemon', 'qty' => 3, 'unit' => 'pcs', 'name_ar' => 'ليمون'],
                ['name_en' => 'water', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'ماء'],
                ['name_en' => 'sugar', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'سكر'],
            ],

            'Smoothie' => [
                ['name_en' => 'banana', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'موز'],
                ['name_en' => 'milk', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'لبن'],
                ['name_en' => 'yogurt', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'زبادي'],
                ['name_en' => 'honey', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'عسل'],
            ],

            'Cookies' => [
                ['name_en' => 'flour', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'دقيق'],
                ['name_en' => 'egg', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'بيض'],
                ['name_en' => 'butter', 'qty' => 100, 'unit' => 'g', 'name_ar' => 'زبدة'],
                ['name_en' => 'sugar', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'سكر'],
                ['name_en' => 'chocolate', 'qty' => 100, 'unit' => 'g', 'name_ar' => 'شوكولاتة'],
            ],
            'Pizza' => [
                ['name_en' => 'flour', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'دقيق'],
                ['name_en' => 'cheese', 'qty' => 200, 'unit' => 'g', 'name_ar' => 'جبنة'],
                ['name_en' => 'butter', 'qty' => 50, 'unit' => 'g', 'name_ar' => 'زبدة'],
                ['name_en' => 'egg', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'بيض'],
                ['name_en' => 'meat', 'qty' => 150, 'unit' => 'g', 'name_ar' => 'لحمة'],
            ],

            'Grilled Fish' => [
                ['name_en' => 'fish', 'qty' => 1, 'unit' => 'kg', 'name_ar' => 'سمك'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
                ['name_en' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'name_ar' => 'فلفل'],
                ['name_en' => 'lemon', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'ليمون'],
                ['name_en' => 'olive oil', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'زيت زيتون'],
            ],

            'Fried Fish' => [
                ['name_en' => 'fish', 'qty' => 1, 'unit' => 'kg', 'name_ar' => 'سمك'],
                ['name_en' => 'flour', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'دقيق'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
                ['name_en' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'name_ar' => 'فلفل'],
                ['name_en' => 'oil', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'زيت'],
            ],

            'Shrimp Pasta' => [
                ['name_en' => 'shrimp', 'qty' => 300, 'unit' => 'g', 'name_ar' => 'جمبري'],
                ['name_en' => 'pasta', 'qty' => 250, 'unit' => 'g', 'name_ar' => 'مكرونة'],
                ['name_en' => 'garlic', 'qty' => 3, 'unit' => 'cloves', 'name_ar' => 'ثوم'],
                ['name_en' => 'olive oil', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'زيت زيتون'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
            ],

            'Seafood Rice' => [
                ['name_en' => 'rice', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'أرز'],
                ['name_en' => 'shrimp', 'qty' => 200, 'unit' => 'g', 'name_ar' => 'جمبري'],
                ['name_en' => 'fish', 'qty' => 200, 'unit' => 'g', 'name_ar' => 'سمك'],
                ['name_en' => 'spices', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'بهارات'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
            ],

            'Tuna Salad' => [
                ['name_en' => 'tuna', 'qty' => 200, 'unit' => 'g', 'name_ar' => 'تونة'],
                ['name_en' => 'lettuce', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'خس'],
                ['name_en' => 'tomato', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'طماطم'],
                ['name_en' => 'olive oil', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'زيت زيتون'],
                ['name_en' => 'lemon', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'ليمون'],
            ],

            'Shrimp Soup' => [
                ['name_en' => 'shrimp', 'qty' => 300, 'unit' => 'g', 'name_ar' => 'جمبري'],
                ['name_en' => 'onion', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'بصل'],
                ['name_en' => 'garlic', 'qty' => 3, 'unit' => 'cloves', 'name_ar' => 'ثوم'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
                ['name_en' => 'water', 'qty' => 4, 'unit' => 'cups', 'name_ar' => 'ماء'],
            ],

            'Fish Sandwich' => [
                ['name_en' => 'fish', 'qty' => 200, 'unit' => 'g', 'name_ar' => 'سمك'],
                ['name_en' => 'bun', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'عيش'],
                ['name_en' => 'lettuce', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'خس'],
                ['name_en' => 'tomato', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'طماطم'],
                ['name_en' => 'sauce', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'صوص'],
            ],

        ];

        foreach ($recipes as $recipe) {

            if (!isset($map[$recipe->title])) continue;

            $syncData = [];

            foreach ($map[$recipe->title] as $index => $row) {

                $ingredient = Ingredient::firstOrCreate([
                    'name_en' => strtolower($row['name_en'])
                ]);

                $syncData[$ingredient->id] = [
                    'quantity' => $row['qty'],
                    'unit' => $row['unit'],
                    'ingredient_name_ar' => $row['name_ar'],
                    'display_text' => $row['qty'] . ' ' . $row['unit'] . ' ' . $row['name_en'],
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            $recipe->ingredients()->sync($syncData);
        }
    }
}
