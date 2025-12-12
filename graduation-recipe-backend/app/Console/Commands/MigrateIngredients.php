<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Console\Command;

class MigrateIngredients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:ingredients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recipes = Recipe::all();

        foreach ($recipes as $recipe) {

            $ingredients = json_decode($recipe->ingredients, true);

            if (!$ingredients) continue;

            foreach ($ingredients as $text) {

                // extract main ingredient (basic)
                $main = strtolower(trim(preg_replace('/[^a-zA-Z ]/', '', $text)));

                if (!$main) continue;

                // save if not exists
                $ingredient = Ingredient::firstOrCreate(['name' => $main]);

                // attach pivot
                $recipe->ingredients()->syncWithoutDetaching([$ingredient->id]);
            }
        }

        $this->info("Ingredients migrated successfully!");
    }
}
