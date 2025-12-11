<?php

namespace Database\Seeders;

use App\Models\ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $ingredients = [
            "Tomato", "Onion", "Garlic", "Potato", "Carrot", "Cucumber", "Lettuce",
            "Chicken Breast", "Beef", "Egg", "Salmon", "Tuna",
            "Rice", "Pasta", "Bread", "Flour", "Sugar", "Salt", "Pepper",
            "Milk", "Cheese", "Butter", "Yogurt", "Cream",
            "Olive Oil", "Lemon", "Basil", "Parsley", "Chili Flakes"
        ];
        foreach ($ingredients as $name){
            ingredient::firstorCreate(['name' => $name]);
        }
    }
}
