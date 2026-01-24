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

            'Beef Steak' => [
                ['name_en' => 'beef', 'qty' => 300, 'unit' => 'g', 'name_ar' => 'لحم'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
                ['name_en' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'name_ar' => 'فلفل'],
                ['name_en' => 'butter', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'زبدة'],
                ['name_en' => 'garlic', 'qty' => 2, 'unit' => 'cloves', 'name_ar' => 'ثوم'],
            ],

            'Lamb Chops' => [
                ['name_en' => 'lamb', 'qty' => 400, 'unit' => 'g', 'name_ar' => 'لحم ضأن'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
                ['name_en' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'name_ar' => 'فلفل'],
                ['name_en' => 'rosemary', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'إكليل الجبل'],
                ['name_en' => 'olive oil', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'زيت زيتون'],
            ],

            'Vegetable Stir Fry' => [
                ['name_en' => 'broccoli', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'بروكلي'],
                ['name_en' => 'carrot', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'جزر'],
                ['name_en' => 'bell pepper', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'فلفل ملون'],
                ['name_en' => 'soy sauce', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'صلصة صويا'],
                ['name_en' => 'garlic', 'qty' => 2, 'unit' => 'cloves', 'name_ar' => 'ثوم'],
            ],

            'Meatballs' => [
                ['name_en' => 'beef', 'qty' => 300, 'unit' => 'g', 'name_ar' => 'لحم'],
                ['name_en' => 'onion', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'بصل'],
                ['name_en' => 'breadcrumbs', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'فتات الخبز'],
                ['name_en' => 'egg', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'بيض'],
                ['name_en' => 'tomato sauce', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'صلصة طماطم'],
            ],

            'Roast Chicken' => [
                ['name_en' => 'chicken', 'qty' => 1, 'unit' => 'kg', 'name_ar' => 'فراخ'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
                ['name_en' => 'pepper', 'qty' => 0.5, 'unit' => 'tsp', 'name_ar' => 'فلفل'],
                ['name_en' => 'herbs', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'أعشاب'],
                ['name_en' => 'lemon', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'ليمون'],
            ],

            'Chocolate Cake' => [
                ['name_en' => 'flour', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'دقيق'],
                ['name_en' => 'cocoa', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'كاكاو'],
                ['name_en' => 'sugar', 'qty' => 1.5, 'unit' => 'cups', 'name_ar' => 'سكر'],
                ['name_en' => 'egg', 'qty' => 3, 'unit' => 'pcs', 'name_ar' => 'بيض'],
                ['name_en' => 'butter', 'qty' => 100, 'unit' => 'g', 'name_ar' => 'زبدة'],
            ],

            'Brownies' => [
                ['name_en' => 'chocolate', 'qty' => 100, 'unit' => 'g', 'name_ar' => 'شوكولاتة'],
                ['name_en' => 'butter', 'qty' => 100, 'unit' => 'g', 'name_ar' => 'زبدة'],
                ['name_en' => 'sugar', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'سكر'],
                ['name_en' => 'egg', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'بيض'],
                ['name_en' => 'flour', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'دقيق'],
            ],

            'Cheesecake' => [
                ['name_en' => 'cream cheese', 'qty' => 500, 'unit' => 'g', 'name_ar' => 'جبن كريمي'],
                ['name_en' => 'sugar', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'سكر'],
                ['name_en' => 'egg', 'qty' => 3, 'unit' => 'pcs', 'name_ar' => 'بيض'],
                ['name_en' => 'graham cracker', 'qty' => 1.5, 'unit' => 'cups', 'name_ar' => 'بسكويت جراهام'],
                ['name_en' => 'vanilla', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'فانيليا'],
            ],

            'Tiramisu' => [
                ['name_en' => 'ladyfinger', 'qty' => 24, 'unit' => 'pcs', 'name_ar' => 'بسكويت الإصبع'],
                ['name_en' => 'mascarpone', 'qty' => 300, 'unit' => 'g', 'name_ar' => 'ماسكاربوني'],
                ['name_en' => 'coffee', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'قهوة'],
                ['name_en' => 'cocoa', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'كاكاو'],
                ['name_en' => 'egg', 'qty' => 3, 'unit' => 'pcs', 'name_ar' => 'بيض'],
            ],

            'Ice Cream' => [
                ['name_en' => 'cream', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'قشطة'],
                ['name_en' => 'milk', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'لبن'],
                ['name_en' => 'sugar', 'qty' => 0.75, 'unit' => 'cup', 'name_ar' => 'سكر'],
                ['name_en' => 'vanilla', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'فانيليا'],
                ['name_en' => 'egg', 'qty' => 4, 'unit' => 'pcs', 'name_ar' => 'بيض'],
            ],

            'Orange Juice' => [
                ['name_en' => 'orange', 'qty' => 4, 'unit' => 'pcs', 'name_ar' => 'برتقال'],
                ['name_en' => 'water', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'ماء'],
                ['name_en' => 'sugar', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'سكر'],
            ],

            'Strawberry Juice' => [
                ['name_en' => 'strawberry', 'qty' => 250, 'unit' => 'g', 'name_ar' => 'فراولة'],
                ['name_en' => 'water', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'ماء'],
                ['name_en' => 'honey', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'عسل'],
            ],

            'Mango Lassi' => [
                ['name_en' => 'mango', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'مانجو'],
                ['name_en' => 'yogurt', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'زبادي'],
                ['name_en' => 'milk', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'لبن'],
                ['name_en' => 'honey', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'عسل'],
            ],

            'Iced Tea' => [
                ['name_en' => 'tea', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'شاي'],
                ['name_en' => 'water', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'ماء'],
                ['name_en' => 'lemon', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'ليمون'],
                ['name_en' => 'sugar', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'سكر'],
            ],

            'Coffee' => [
                ['name_en' => 'coffee beans', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'حبوب القهوة'],
                ['name_en' => 'water', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'ماء'],
                ['name_en' => 'sugar', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'سكر'],
            ],

            'Nachos' => [
                ['name_en' => 'tortilla chips', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'رقائق التورتيلا'],
                ['name_en' => 'cheese', 'qty' => 150, 'unit' => 'g', 'name_ar' => 'جبنة'],
                ['name_en' => 'jalapeño', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'فلفل حار'],
                ['name_en' => 'salsa', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'صلصة'],
            ],

            'Popcorn' => [
                ['name_en' => 'popcorn kernels', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'حبات الفشار'],
                ['name_en' => 'butter', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'زبدة'],
                ['name_en' => 'salt', 'qty' => 1, 'unit' => 'tsp', 'name_ar' => 'ملح'],
            ],

            'Chips and Guacamole' => [
                ['name_en' => 'tortilla chips', 'qty' => 2, 'unit' => 'cups', 'name_ar' => 'رقائق التورتيلا'],
                ['name_en' => 'avocado', 'qty' => 2, 'unit' => 'pcs', 'name_ar' => 'أفوكادو'],
                ['name_en' => 'lime', 'qty' => 1, 'unit' => 'pcs', 'name_ar' => 'ليمون أخضر'],
                ['name_en' => 'cilantro', 'qty' => 2, 'unit' => 'tbsp', 'name_ar' => 'كزبرة'],
                ['name_en' => 'salt', 'qty' => 0.5, 'unit' => 'tsp', 'name_ar' => 'ملح'],
            ],

            'Fruit Trail Mix' => [
                ['name_en' => 'almonds', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'لوز'],
                ['name_en' => 'raisins', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'زبيب'],
                ['name_en' => 'dried cranberry', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'توت برّي مجفف'],
                ['name_en' => 'dark chocolate', 'qty' => 100, 'unit' => 'g', 'name_ar' => 'شوكولاتة داكنة'],
            ],

            'Yogurt Parfait' => [
                ['name_en' => 'yogurt', 'qty' => 1, 'unit' => 'cup', 'name_ar' => 'زبادي'],
                ['name_en' => 'granola', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'جرانولا'],
                ['name_en' => 'berry', 'qty' => 0.5, 'unit' => 'cup', 'name_ar' => 'توت'],
                ['name_en' => 'honey', 'qty' => 1, 'unit' => 'tbsp', 'name_ar' => 'عسل'],
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
