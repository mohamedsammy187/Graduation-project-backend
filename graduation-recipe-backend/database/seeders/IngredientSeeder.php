<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            // Vegetables
            "Tomato", "Onion", "Garlic", "Potato", "Carrot", "Cucumber", "Lettuce",

            // Proteins
            "Chicken Breast", "Beef", "Egg",
            "Fish", "Shrimp", "Salmon", "Tuna",

            // Carbs
            "Rice", "Pasta", "Bread", "Bun", "Flour",

            // Dairy
            "Milk", "Cheese", "Butter", "Yogurt", "Cream",

            // Others
            "Salt", "Pepper", "Sugar", "Olive Oil", "Lemon",
            "Parsley", "Basil", "Spices", "Sauce", "Oil", "Water"
        ];

        foreach ($ingredients as $name) {
            Ingredient::firstOrCreate([
                'name' => strtolower($name) // مهم للماتشينج
            ]);
        }
    }
}
