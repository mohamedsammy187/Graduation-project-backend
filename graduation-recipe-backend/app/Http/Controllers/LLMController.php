<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class LLMController extends Controller
{
    public function ask(Request $request)
    {
        try {
            $apiKey = env('GEMINI_API_KEY');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

            // 1. Get recipes from DB
            $recipes = DB::table('recipes')->get();

            // 2. Build recipes context
            $recipesText = "";
            foreach ($recipes as $recipe) {
                $ingredients = implode(', ', json_decode($recipe->ingredients, true) ?? []);
                $steps = implode(' | ', json_decode($recipe->steps, true) ?? []);

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
List ingredients clearly for shooping.
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
