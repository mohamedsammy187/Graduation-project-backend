<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Recipe;

class LLMController extends Controller
{
    public function ask(Request $request)
    {
        try {
            $apiKey = env('GEMINI_API_KEY');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

            // 1. Get recipes from DB
            $recipes = Recipe::all();

            // 2. Build recipes context
            $recipesText = "";
            foreach ($recipes as $recipe) {

                $ingredientsArr = json_decode($recipe->ingredients, true) ?? [];
                $stepsArr = json_decode($recipe->steps, true) ?? [];

                $ingredients = implode(', ', $ingredientsArr);
                $steps = implode(' | ', $stepsArr);

                // ✅ build link per recipe
                $link = url("/api/recipes/slug/{$recipe->slug}");

                $recipesText .= "Link: {$link}\n";
                $recipesText .= "Recipe: {$recipe->title}\n";
                $recipesText .= "Ingredients: {$ingredients}\n";
                $recipesText .= "Steps: {$steps}\n\n";
            }

            // 3. Build final prompt
            $userQuestion = $request->input('prompt');

            $finalPrompt = "
You are a recipe assistant.
You must answer ONLY using the recipes provided below.
Always include the recipe link when suggesting one.
When recommending a recipe, list its ingredients clearly for a shopping list.
If the answer is not in the recipes, say:
'Sorry, I don't have this information in my recipes database.'

Recipes:
{$recipesText}

User question: {$userQuestion}
";

            // 4. Call Gemini
            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $finalPrompt]
                        ]
                    ]
                ]
            ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gemini request failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
