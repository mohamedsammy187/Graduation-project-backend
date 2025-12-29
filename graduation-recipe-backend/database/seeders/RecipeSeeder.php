<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;

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
                'ingredients' => json_encode([
                    "1 cup flour",
                    "1 egg",
                    "1 cup milk",
                    "2 tbsp sugar",
                    "1 tsp baking powder"
                ]),
                'steps' => json_encode([
                    "Mix all ingredients",
                    "Heat a pan",
                    "Pour batter and cook both sides"
                ]),
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
                'ingredients' => json_encode([
                    "2 eggs",
                    "Salt",
                    "Pepper",
                    "1 tbsp butter",
                    "Optional: vegetables"
                ]),
                'steps' => json_encode([
                    "Beat the eggs",
                    "Heat butter in pan",
                    "Pour eggs and cook until set"
                ]),
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
                'ingredients' => json_encode([
                    "2 slices bread",
                    "1 egg",
                    "1/4 cup milk",
                    "1 tsp sugar",
                    "1 tsp cinnamon"
                ]),
                'steps' => json_encode([
                    "Beat egg, milk, sugar, cinnamon",
                    "Dip bread in mixture",
                    "Cook on pan until golden"
                ]),
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
                'ingredients' => json_encode([
                    "1/2 cup oats",
                    "1 cup milk or water",
                    "Honey or fruits"
                ]),
                'steps' => json_encode([
                    "Boil milk or water",
                    "Add oats and cook 3-5 min",
                    "Add honey or fruits"
                ]),
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
                'ingredients' => json_encode([
                    "200g chicken breast",
                    "Salt",
                    "Pepper",
                    "Olive oil",
                    "Herbs"
                ]),
                'steps' => json_encode([
                    "Season the chicken",
                    "Grill on pan or oven",
                    "Serve hot"
                ]),
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
                'ingredients' => json_encode([
                    "200g pasta",
                    "100g chicken or vegetables",
                    "1 cup sauce",
                    "Salt",
                    "Pepper"
                ]),
                'steps' => json_encode([
                    "Boil pasta",
                    "Cook chicken/vegetables",
                    "Mix with sauce"
                ]),
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
                'ingredients' => json_encode([
                    "200g beef patty",
                    "Burger bun",
                    "Cheese",
                    "Lettuce",
                    "Tomato"
                ]),
                'steps' => json_encode([
                    "Cook beef patty",
                    "Assemble burger with bun and toppings"
                ]),
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
                'ingredients' => json_encode([
                    "200g chicken",
                    "1 cup rice",
                    "Spices",
                    "Salt",
                    "Oil"
                ]),
                'steps' => json_encode([
                    "Cook rice",
                    "Cook chicken with spices",
                    "Mix and serve"
                ]),
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
                'ingredients' => json_encode([
                    "2 lemons",
                    "1 cup water",
                    "Sugar or honey"
                ]),
                'steps' => json_encode([
                    "Squeeze lemons",
                    "Mix with water and sugar",
                    "Serve chilled"
                ]),
            ],
            [
                'title' => 'Smoothie',
                'category' => 'drink',
                'meal_type' => 'dinner',
                'temperature' => 'hot',
                'image' => 'asset/img/smoothie.jpg',
                'time' => '10 min',
                'difficulty' => 'Easy',
                'calories' => '200',
                'ingredients' => json_encode([
                    "1 banana",
                    "1 cup milk or yogurt",
                    "Fruits of choice",
                    "Honey"
                ]),
                'steps' => json_encode([
                    "Blend all ingredients",
                    "Serve immediately"
                ]),
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
                'ingredients' => json_encode([
                    "Assorted fruits",
                    "Honey or yogurt"
                ]),
                'steps' => json_encode([
                    "Cut fruits",
                    "Mix with honey or yogurt",
                    "Serve fresh"
                ]),
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
                'ingredients' => json_encode([
                    "1 cup flour",
                    "1/2 cup sugar",
                    "1/2 cup butter",
                    "1 egg",
                    "Chocolate chips"
                ]),
                'steps' => json_encode([
                    "Mix all ingredients",
                    "Shape cookies",
                    "Bake 12-15 min at 180°C"
                ]),
            ],
            [
                'title' => 'Pizza',
                'category' => 'meal',
                'meal_type' => 'lunch',
                'temperature' => 'cold',
                'image' => 'asset/img/p3.jpeg',
                'time' => '25 min',
                'difficulty' => 'Medium',
                'calories' => '300',
                'ingredients' => json_encode([
                    "1 cup flour",
                    "1/2 cup cheese",
                    "1/2 cup butter",
                    "1 egg",
                    "meat chips"
                ]),
                'steps' => json_encode([
                    "Mix all ingredients",
                    "Shape cookies",
                    "Bake 12-15 min at 180°C"
                ]),
            ],



        ];

        foreach ($recipes as $recipe) {
            \App\Models\Recipe::create($recipe);
        }
    }
}
