<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;

class IngredientRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = Ingredient::all()->keyBy('name');
        $recipes = Recipe::all();

        // 🔥 Smart Recipe Ingredient Map (with quantities)
        $map = [

            'Pancakes' => [
                ['name' => 'egg', 'qty' => 2, 'unit' => 'pcs', 'ar' => 'بيض'],
                ['name' => 'flour', 'qty' => 1, 'unit' => 'cup', 'ar' => 'دقيق'],
                ['name' => 'milk', 'qty' => 1, 'unit' => 'cup', 'ar' => 'لبن'],
                ['name' => 'sugar', 'qty' => 1, 'unit' => 'tbsp', 'ar' => 'سكر'],
            ],

            'Omelette' => [
                ['name' => 'egg', 'qty' => 3, 'unit' => 'pcs', 'ar' => 'بيض'],
                ['name' => 'butter', 'qty' => 1, 'unit' => 'tbsp', 'ar' => 'زبدة'],
                ['name' => 'salt', 'qty' => 0.5, 'unit' => 'tsp', 'ar' => 'ملح'],
                ['name' => 'pepper', 'qty' => 0.25, 'unit' => 'tsp', 'ar' => 'فلفل'],
            ],

            'French Toast' => [
                ['name' => 'bread', 'qty' => 4, 'unit' => 'slices', 'ar' => 'عيش'],
                ['name' => 'egg', 'qty' => 2, 'unit' => 'pcs', 'ar' => 'بيض'],
                ['name' => 'milk', 'qty' => 0.5, 'unit' => 'cup', 'ar' => 'لبن'],
                ['name' => 'sugar', 'qty' => 1, 'unit' => 'tbsp', 'ar' => 'سكر'],
            ],

            'Oats' => [
                ['name' => 'oats', 'qty' => 1, 'unit' => 'cup', 'ar' => 'شوفان'],
                ['name' => 'milk', 'qty' => 1, 'unit' => 'cup', 'ar' => 'لبن'],
                ['name' => 'honey', 'qty' => 1, 'unit' => 'tbsp', 'ar' => 'عسل'],
            ],

            'Grilled Chicken' => [
                ['name' => 'chicken', 'qty' => 500, 'unit' => 'g', 'ar' => 'فراخ'],
                ['name' => 'olive oil', 'qty' => 2, 'unit' => 'tbsp', 'ar' => 'زيت زيتون'],
                ['name' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'ar' => 'ملح'],
                ['name' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'ar' => 'فلفل'],
                ['name' => 'herbs', 'qty' => 1, 'unit' => 'tbsp', 'ar' => 'أعشاب'],
            ],

            'Pasta' => [
                ['name' => 'pasta', 'qty' => 200, 'unit' => 'g', 'ar' => 'مكرونة'],
                ['name' => 'chicken', 'qty' => 200, 'unit' => 'g', 'ar' => 'فراخ'],
                ['name' => 'sauce', 'qty' => 1, 'unit' => 'cup', 'ar' => 'صلصة'],
                ['name' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'ar' => 'ملح'],
                ['name' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'ar' => 'فلفل'],
            ],

            'Burger' => [
                ['name' => 'beef', 'qty' => 200, 'unit' => 'g', 'ar' => 'لحم'],
                ['name' => 'bun', 'qty' => 2, 'unit' => 'pcs', 'ar' => 'خبز'],
                ['name' => 'cheese', 'qty' => 2, 'unit' => 'slices', 'ar' => 'جبنة'],
                ['name' => 'lettuce', 'qty' => 1, 'unit' => 'cup', 'ar' => 'خس'],
                ['name' => 'tomato', 'qty' => 1, 'unit' => 'pcs', 'ar' => 'طماطم'],
            ],

            'Lemon Juice' => [
                ['name' => 'lemon', 'qty' => 3, 'unit' => 'pcs', 'ar' => 'ليمون'],
                ['name' => 'water', 'qty' => 2, 'unit' => 'cups', 'ar' => 'ماء'],
                ['name' => 'sugar', 'qty' => 2, 'unit' => 'tbsp', 'ar' => 'سكر'],
            ],

            'Smoothie' => [
                ['name' => 'banana', 'qty' => 2, 'unit' => 'pcs', 'ar' => 'موز'],
                ['name' => 'milk', 'qty' => 1, 'unit' => 'cup', 'ar' => 'لبن'],
                ['name' => 'yogurt', 'qty' => 0.5, 'unit' => 'cup', 'ar' => 'زبادي'],
                ['name' => 'honey', 'qty' => 1, 'unit' => 'tbsp', 'ar' => 'عسل'],
            ],

            'Cookies' => [
                ['name' => 'flour', 'qty' => 2, 'unit' => 'cups', 'ar' => 'دقيق'],
                ['name' => 'egg', 'qty' => 2, 'unit' => 'pcs', 'ar' => 'بيض'],
                ['name' => 'butter', 'qty' => 100, 'unit' => 'g', 'ar' => 'زبدة'],
                ['name' => 'sugar', 'qty' => 1, 'unit' => 'cup', 'ar' => 'سكر'],
                ['name' => 'chocolate', 'qty' => 100, 'unit' => 'g', 'ar' => 'شوكولاتة'],
            ],
            'Pizza' => [
                ['name' => 'flour', 'qty' => 2, 'unit' => 'cups', 'ar' => 'دقيق'],
                ['name' => 'cheese', 'qty' => 200, 'unit' => 'g', 'ar' => 'جبنة'],
                ['name' => 'butter', 'qty' => 50, 'unit' => 'g', 'ar' => 'زبدة'],
                ['name' => 'egg', 'qty' => 2, 'unit' => 'pcs', 'ar' => 'بيض'],
                ['name' => 'meat', 'qty' => 150, 'unit' => 'g', 'ar' => 'لحمة'],
            ],

            'Grilled Fish' => [
                ['name' => 'fish', 'qty' => 1, 'unit' => 'kg', 'ar' => 'سمك'],
                ['name' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'ar' => 'ملح'],
                ['name' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'ar' => 'فلفل'],
                ['name' => 'lemon', 'qty' => 2, 'unit' => 'pcs', 'ar' => 'ليمون'],
                ['name' => 'olive oil', 'qty' => 2, 'unit' => 'tbsp', 'ar' => 'زيت زيتون'],
            ],

            'Fried Fish' => [
                ['name' => 'fish', 'qty' => 1, 'unit' => 'kg', 'ar' => 'سمك'],
                ['name' => 'flour', 'qty' => 1, 'unit' => 'cup', 'ar' => 'دقيق'],
                ['name' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'ar' => 'ملح'],
                ['name' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'ar' => 'فلفل'],
                ['name' => 'oil', 'qty' => 2, 'unit' => 'cups', 'ar' => 'زيت'],
            ],

            'Shrimp Pasta' => [
                ['name' => 'shrimp', 'qty' => 300, 'unit' => 'g', 'ar' => 'جمبري'],
                ['name' => 'pasta', 'qty' => 250, 'unit' => 'g', 'ar' => 'مكرونة'],
                ['name' => 'garlic', 'qty' => 3, 'unit' => 'cloves', 'ar' => 'ثوم'],
                ['name' => 'olive oil', 'qty' => 2, 'unit' => 'tbsp', 'ar' => 'زيت زيتون'],
                ['name' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'ar' => 'ملح'],
            ],

            'Seafood Rice' => [
                ['name' => 'rice', 'qty' => 2, 'unit' => 'cups', 'ar' => 'أرز'],
                ['name' => 'shrimp', 'qty' => 200, 'unit' => 'g', 'ar' => 'جمبري'],
                ['name' => 'fish', 'qty' => 200, 'unit' => 'g', 'ar' => 'سمك'],
                ['name' => 'spices', 'qty' => 1, 'unit' => 'tbsp', 'ar' => 'بهارات'],
                ['name' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'ar' => 'ملح'],
            ],

            'Tuna Salad' => [
                ['name' => 'tuna', 'qty' => 200, 'unit' => 'g', 'ar' => 'تونة'],
                ['name' => 'lettuce', 'qty' => 2, 'unit' => 'cups', 'ar' => 'خس'],
                ['name' => 'tomato', 'qty' => 2, 'unit' => 'pcs', 'ar' => 'طماطم'],
                ['name' => 'olive oil', 'qty' => 2, 'unit' => 'tbsp', 'ar' => 'زيت زيتون'],
                ['name' => 'lemon', 'qty' => 1, 'unit' => 'pcs', 'ar' => 'ليمون'],
            ],

            'Shrimp Soup' => [
                ['name' => 'shrimp', 'qty' => 300, 'unit' => 'g', 'ar' => 'جمبري'],
                ['name' => 'onion', 'qty' => 1, 'unit' => 'pcs', 'ar' => 'بصل'],
                ['name' => 'garlic', 'qty' => 3, 'unit' => 'cloves', 'ar' => 'ثوم'],
                ['name' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'ar' => 'ملح'],
                ['name' => 'water', 'qty' => 4, 'unit' => 'cups', 'ar' => 'ماء'],
            ],

            'Fish Sandwich' => [
                ['name' => 'fish', 'qty' => 200, 'unit' => 'g', 'ar' => 'سمك'],
                ['name' => 'bun', 'qty' => 2, 'unit' => 'pcs', 'ar' => 'عيش'],
                ['name' => 'lettuce', 'qty' => 1, 'unit' => 'cup', 'ar' => 'خس'],
                ['name' => 'tomato', 'qty' => 1, 'unit' => 'pcs', 'ar' => 'طماطم'],
                ['name' => 'sauce', 'qty' => 2, 'unit' => 'tbsp', 'ar' => 'صوص'],
            ],

        ];

        foreach ($recipes as $recipe) {

            if (!isset($map[$recipe->title])) continue;

            $syncData = [];

            foreach ($map[$recipe->title] as $index => $row) {

                $ingredient = Ingredient::firstOrCreate([
                    'name' => strtolower($row['name'])
                ]);

                $syncData[$ingredient->id] = [
                    'quantity' => $row['qty'],
                    'unit' => $row['unit'],
                    'ingredient_name_ar' => $row['ar'],
                    'display_text' => $row['qty'] . ' ' . $row['unit'] . ' ' . $row['name'],
                    'sort_order' => $index
                ];
            }

            $recipe->ingredients()->sync($syncData);
        }
    }
}
