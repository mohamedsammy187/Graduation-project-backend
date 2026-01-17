<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // \App\Models\Recipe::factory(10)->create();
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            IngredientSeeder::class,
            RecipeSeeder::class,
            PantrySeeder::class,
            IngredientRecipeSeeder::class
        ]);
    }
}
