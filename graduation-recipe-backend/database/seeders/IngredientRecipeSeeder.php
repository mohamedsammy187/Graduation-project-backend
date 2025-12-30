<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;

class IngredientRecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Get all ingredients
        $ingredients = Ingredient::all()->keyBy('name'); // key by name for easy matching

        // Get all recipes
        $recipes = Recipe::all();

        foreach ($recipes as $recipe) {

            $ingredientIds = [];

            // You need some source of ingredients for each recipe
            // For example, if your RecipeSeeder stored them in a temporary array, use it here
            // Otherwise, you can define them inline here as mapping:
            $recipeIngredients = match ($recipe->title) {
                'Pancakes' => ['flour', 'egg', 'milk', 'sugar'],
                'Omelette' => ['egg', 'salt', 'pepper', 'butter'],
                'French Toast' => ['bread', 'egg', 'milk', 'sugar'],
                'Oats' => ['oats', 'milk', 'honey'],
                'Grilled Chicken' => ['chicken', 'salt', 'pepper', 'olive oil', 'herbs'],
                'Pasta' => ['pasta', 'chicken', 'sauce', 'salt', 'pepper'],
                'Burger' => ['beef', 'bun', 'cheese', 'lettuce', 'tomato'],
                'Chicken Rice' => ['chicken', 'rice', 'spices', 'salt', 'oil'],
                'Lemon Juice' => ['lemon', 'water', 'sugar'],
                'Smoothie' => ['banana', 'milk', 'yogurt', 'honey'],
                'Fruit Salad' => ['apple', 'banana', 'orange', 'yogurt'],
                'Cookies' => ['flour', 'sugar', 'butter', 'egg', 'chocolate'],
                'Pizza' => ['flour', 'cheese', 'butter', 'egg', 'meat'],
                'Grilled Fish' => ['fish', 'salt', 'pepper', 'lemon', 'olive oil'],
                'Fried Fish' => ['fish', 'flour', 'salt', 'pepper', 'oil'],
                'Shrimp Pasta' => ['shrimp', 'pasta', 'garlic', 'olive oil', 'salt'],
                'Seafood Rice' => ['rice', 'shrimp', 'fish', 'spices', 'salt'],
                'Tuna Salad' => ['tuna', 'lettuce', 'tomato', 'olive oil', 'lemon'],
                'Shrimp Soup' => ['shrimp', 'onion', 'garlic', 'salt', 'water'],
                'Fish Sandwich' => ['fish', 'bun', 'lettuce', 'tomato', 'sauce'],
                default => []
            };


            foreach ($recipeIngredients as $name) {
                if (isset($ingredients[strtolower($name)])) {
                    $ingredientIds[] = $ingredients[strtolower($name)]->id;
                } else {
                    // Optional: create missing ingredient
                    $ingredient = Ingredient::firstOrCreate([
                        'name' => strtolower($name)
                    ]);
                    $ingredientIds[] = $ingredient->id;
                    $ingredients[strtolower($name)] = $ingredient;
                }
            }

            // Attach ingredients to recipe
            $recipe->ingredients()->sync($ingredientIds);
        }
    }
}
