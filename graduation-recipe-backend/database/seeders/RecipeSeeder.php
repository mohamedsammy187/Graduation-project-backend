<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;

class RecipeSeeder extends Seeder
{
    public function run()
    {
        $recipes = [
            [
                'title' => 'Pancakes',
                'category' => 'meal',
                'meal_type' => 'breakfast',
                'temperature' => 'cold',
                'image' => 'asset/img/pancakes.jpg',
                'time' => '20 min',
                'difficulty' => 'Easy',
                'calories' => '350',
                'ingredients' => ['flour', 'egg', 'milk', 'sugar'],
                'steps' => [
                    "Mix all ingredients",
                    "Heat a pan",
                    "Pour batter and cook both sides"
                ],
            ],
            [
                'title' => 'Omelette',
                'category' => 'meal',
                'meal_type' => 'breakfast',
                'temperature' => 'hot',
                'image' => 'asset/img/omelette.jpg',
                'time' => '10 min',
                'difficulty' => 'Easy',
                'calories' => '200',
                'ingredients' => ['egg', 'salt', 'pepper', 'butter'],
                'steps' => [
                    "Beat the eggs",
                    "Heat butter in pan",
                    "Pour eggs and cook until set"
                ],
            ],
            [
                'title' => 'French Toast',
                'category' => 'meal',
                'meal_type' => 'breakfast',
                'temperature' => 'cold',
                'image' => 'asset/img/french_toast.jpg',
                'time' => '15 min',
                'difficulty' => 'Easy',
                'calories' => '250',
                'ingredients' => ['bread', 'egg', 'milk', 'sugar'],
                'steps' => [
                    "Beat egg, milk, sugar",
                    "Dip bread in mixture",
                    "Cook on pan until golden"
                ],
            ],
            [
                'title' => 'Oats',
                'category' => 'meal',
                'meal_type' => 'breakfast',
                'temperature' => 'hot',
                'image' => 'asset/img/oats.jpg',
                'time' => '5 min',
                'difficulty' => 'Easy',
                'calories' => '150',
                'ingredients' => ['oats', 'milk', 'honey'],
                'steps' => [
                    "Boil milk or water",
                    "Add oats and cook 3-5 min",
                    "Add honey or fruits"
                ],
            ],
            [
                'title' => 'Grilled Chicken',
                'category' => 'meal',
                'meal_type' => 'lunch',
                'temperature' => 'hot',
                'image' => 'asset/img/grilled_chicken.jpg',
                'time' => '40 min',
                'difficulty' => 'Medium',
                'calories' => '400',
                'ingredients' => ['chicken', 'salt', 'pepper', 'olive oil', 'herbs'],
                'steps' => [
                    "Season the chicken",
                    "Grill on pan or oven",
                    "Serve hot"
                ],
            ],
            [
                'title' => 'Pasta',
                'category' => 'meal',
                'meal_type' => 'lunch',
                'temperature' => 'hot',
                'image' => 'asset/img/pasta.jpg',
                'time' => '30 min',
                'difficulty' => 'Easy',
                'calories' => '450',
                'ingredients' => ['pasta', 'chicken', 'sauce', 'salt', 'pepper'],
                'steps' => [
                    "Boil pasta",
                    "Cook chicken/vegetables",
                    "Mix with sauce"
                ],
            ],
            [
                'title' => 'Burger',
                'category' => 'meal',
                'meal_type' => 'lunch',
                'temperature' => 'hot',
                'image' => 'asset/img/burger.jpg',
                'time' => '20 min',
                'difficulty' => 'Medium',
                'calories' => '600',
                'ingredients' => ['beef', 'bun', 'cheese', 'lettuce', 'tomato'],
                'steps' => [
                    "Cook beef patty",
                    "Assemble burger with bun and toppings"
                ],
            ],
            [
                'title' => 'Chicken Rice',
                'category' => 'meal',
                'meal_type' => 'lunch',
                'temperature' => 'hot',
                'image' => 'asset/img/chicken_rice.jpg',
                'time' => '45 min',
                'difficulty' => 'Medium',
                'calories' => '500',
                'ingredients' => ['chicken', 'rice', 'spices', 'salt', 'oil'],
                'steps' => [
                    "Cook rice",
                    "Cook chicken with spices",
                    "Mix and serve"
                ],
            ],
            [
                'title' => 'Lemon Juice',
                'category' => 'drink',
                'meal_type' => 'breakfast',
                'temperature' => 'cold',
                'image' => 'asset/img/lemon_juice.jpg',
                'time' => '5 min',
                'difficulty' => 'Easy',
                'calories' => '50',
                'ingredients' => ['lemon', 'water', 'sugar'],
                'steps' => [
                    "Squeeze lemons",
                    "Mix with water and sugar",
                    "Serve chilled"
                ],
            ],
            [
                'title' => 'Smoothie',
                'category' => 'drink',
                'meal_type' => 'dinner',
                'temperature' => 'cold',
                'image' => 'asset/img/smoothie.jpg',
                'time' => '10 min',
                'difficulty' => 'Easy',
                'calories' => '200',
                'ingredients' => ['banana', 'milk', 'yogurt', 'honey'],
                'steps' => [
                    "Blend all ingredients",
                    "Serve immediately"
                ],
            ],
            [
                'title' => 'Fruit Salad',
                'category' => 'meal',
                'meal_type' => 'dinner',
                'temperature' => 'cold',
                'image' => 'asset/img/fruit_salad.jpg',
                'time' => '10 min',
                'difficulty' => 'Easy',
                'calories' => '150',
                'ingredients' => ['apple', 'banana', 'orange', 'yogurt'],
                'steps' => [
                    "Cut fruits",
                    "Mix with honey or yogurt",
                    "Serve fresh"
                ],
            ],
            [
                'title' => 'Cookies',
                'category' => 'snack',
                'meal_type' => 'dinner',
                'temperature' => 'cold',
                'image' => 'asset/img/cookies.jpg',
                'time' => '25 min',
                'difficulty' => 'Medium',
                'calories' => '300',
                'ingredients' => ['flour', 'sugar', 'butter', 'egg', 'chocolate'],
                'steps' => [
                    "Mix all ingredients",
                    "Shape cookies",
                    "Bake 12-15 min at 180°C"
                ],
            ],
            [
                'title' => 'Pizza',
                'category' => 'meal',
                'meal_type' => 'lunch',
                'temperature' => 'hot',
                'image' => 'asset/img/p3.jpeg',
                'time' => '25 min',
                'difficulty' => 'Medium',
                'calories' => '300',
                'ingredients' => ['flour', 'cheese', 'butter', 'egg', 'meat'],
                'steps' => [
                    "Prepare dough",
                    "Add toppings",
                    "Bake until ready"
                ],
            ],

            [
                'title' => 'Grilled Fish',
                'category' => 'meal',
                'meal_type' => 'lunch',
                'temperature' => 'hot',
                'image' => 'asset/img/grilled_fish.jpg',
                'time' => '30 min',
                'difficulty' => 'Easy',
                'calories' => '350',
                'ingredients' => ['fish', 'salt', 'pepper', 'lemon', 'olive oil'],
                'steps' => [
                    "Clean the fish",
                    "Season with salt, pepper and lemon",
                    "Grill until cooked"
                ],
            ],
            [
                'title' => 'Fried Fish',
                'category' => 'meal',
                'meal_type' => 'lunch',
                'temperature' => 'hot',
                'image' => 'asset/img/fried_fish.jpg',
                'time' => '25 min',
                'difficulty' => 'Medium',
                'calories' => '450',
                'ingredients' => ['fish', 'flour', 'salt', 'pepper', 'oil'],
                'steps' => [
                    "Season the fish",
                    "Coat with flour",
                    "Fry in hot oil until golden"
                ],
            ],
            [
                'title' => 'Shrimp Pasta',
                'category' => 'meal',
                'meal_type' => 'dinner',
                'temperature' => 'hot',
                'image' => 'asset/img/shrimp_pasta.jpg',
                'time' => '35 min',
                'difficulty' => 'Medium',
                'calories' => '500',
                'ingredients' => ['shrimp', 'pasta', 'garlic', 'olive oil', 'salt'],
                'steps' => [
                    "Boil pasta",
                    "Cook shrimp with garlic",
                    "Mix shrimp with pasta and olive oil"
                ],
            ],
            [
                'title' => 'Seafood Rice',
                'category' => 'meal',
                'meal_type' => 'lunch',
                'temperature' => 'hot',
                'image' => 'asset/img/seafood_rice.jpg',
                'time' => '40 min',
                'difficulty' => 'Medium',
                'calories' => '520',
                'ingredients' => ['rice', 'shrimp', 'fish', 'spices', 'salt'],
                'steps' => [
                    "Cook rice",
                    "Cook seafood with spices",
                    "Mix seafood with rice and serve"
                ],
            ],
            [
                'title' => 'Tuna Salad',
                'category' => 'meal',
                'meal_type' => 'dinner',
                'temperature' => 'cold',
                'image' => 'asset/img/tuna_salad.jpg',
                'time' => '10 min',
                'difficulty' => 'Easy',
                'calories' => '300',
                'ingredients' => ['tuna', 'lettuce', 'tomato', 'olive oil', 'lemon'],
                'steps' => [
                    "Prepare vegetables",
                    "Add tuna",
                    "Mix with olive oil and lemon"
                ],
            ],
            [
                'title' => 'Shrimp Soup',
                'category' => 'meal',
                'meal_type' => 'dinner',
                'temperature' => 'hot',
                'image' => 'asset/img/shrimp_soup.jpg',
                'time' => '30 min',
                'difficulty' => 'Easy',
                'calories' => '280',
                'ingredients' => ['shrimp', 'onion', 'garlic', 'salt', 'water'],
                'steps' => [
                    "Boil water",
                    "Add shrimp and vegetables",
                    "Cook until shrimp is done"
                ],
            ],
            [
                'title' => 'Fish Sandwich',
                'category' => 'meal',
                'meal_type' => 'lunch',
                'temperature' => 'hot',
                'image' => 'asset/img/fish_sandwich.jpg',
                'time' => '15 min',
                'difficulty' => 'Easy',
                'calories' => '400',
                'ingredients' => ['fish', 'bun', 'lettuce', 'tomato', 'sauce'],
                'steps' => [
                    "Cook the fish",
                    "Prepare bun and vegetables",
                    "Assemble the sandwich"
                ],
            ],


        ];

        foreach ($recipes as $data) {

            // 1️⃣ Create recipe (بدون ingredients)
            $recipe = Recipe::create([
                'title'       => $data['title'],
                'category'    => $data['category'],
                'meal_type'   => $data['meal_type'],
                'temperature' => $data['temperature'],
                'image'       => $data['image'],
                'time'        => $data['time'],
                'difficulty'  => $data['difficulty'],
                'calories'    => $data['calories'],
                'steps'       => json_encode($data['steps']),
            ]);

            // 2️⃣ Attach ingredients
            $ingredientIds = [];

            foreach ($data['ingredients'] as $name) {
                $ingredient = Ingredient::firstOrCreate([
                    'name' => strtolower(trim($name))
                ]);

                $ingredientIds[] = $ingredient->id;
            }

            // 3️⃣ Sync pivot table
            $recipe->ingredients()->sync($ingredientIds);
        }
    }
}
