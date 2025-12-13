<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class FillIngredientRecipe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill:ingredient-recipe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill ingredient_recipe pivot table from recipe JSON ingredients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('ingredient_recipe')->truncate();

        $ingredients = Ingredient::all();

        Recipe::all()->each(function ($recipe) use ($ingredients) {

            $recipeIngredients = json_decode($recipe->ingredients, true);
            if (!$recipeIngredients) return;

            foreach ($recipeIngredients as $text) {
                foreach ($ingredients as $ingredient) {
                    if (stripos($text, $ingredient->name) !== false) {
                        DB::table('ingredient_recipe')->insert([
                            'recipe_id' => $recipe->id,
                            'ingredient_id' => $ingredient->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });

        $this->info('ingredient_recipe table filled successfully');
    }
}
