<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Recipe;
use App\Models\PantryItem;
use App\Models\ShoppingItem;
use Illuminate\Database\Eloquent\Builder;

class SmartChatController extends Controller
{
    public function handle(Request $request)
    {
        $userMessage = $request->input('prompt'); 
        $user = $request->user();

        if (!$userMessage) {
            return response()->json(['message' => 'Please provide a prompt'], 400);
        }

        // 1. تحليل النية
        $aiAnalysis = $this->analyzeIntentWithGroq($userMessage);

        if (isset($aiAnalysis['error'])) {
            return response()->json([
                'response_type' => 'text', 
                'message' => 'System currently unavailable. (' . $aiAnalysis['error'] . ')'
            ], 500);
        }

        // 2. توجيه الطلب
        switch ($aiAnalysis['action']) {
            case 'search_recipe':
                return $this->searchRecipe($user, $aiAnalysis['filters'], $aiAnalysis['reply_text'] ?? null);

            case 'pantry_suggest':
                if (!$user) return response()->json(['response_type' => 'text', 'message' => 'Please login to use pantry features. 🔐']);
                return $this->suggestFromPantry($user, $aiAnalysis['filters'] ?? []);

            case 'get_pantry':
                if (!$user) return response()->json(['response_type' => 'text', 'message' => 'Please login to view your pantry. 🔐']);
                return $this->getPantryContent($user, $aiAnalysis['filters'] ?? []);

            case 'get_shopping_list':
                if (!$user) return response()->json(['response_type' => 'text', 'message' => 'Please login to view your shopping list. 🔐']);
                return $this->getShoppingListContent($user, $aiAnalysis['filters'] ?? []);

            case 'get_full_inventory':
                if (!$user) return response()->json(['response_type' => 'text', 'message' => 'Please login to check your inventory. 🔐']);
                return $this->getFullInventory($user);

            case 'get_favorites':
                if (!$user) return response()->json(['response_type' => 'text', 'message' => 'Please login to view your favorites. 🔐']);
                return $this->getFavorites($user);

            case 'get_help':
                return $this->getHelp($aiAnalysis['filters'] ?? []);

            case 'chit_chat':
            default:
                $reply = $aiAnalysis['reply_text'] ?? "I'm listening! Tell me what ingredients you have or what you're craving. 😋";
                return response()->json(['response_type' => 'text', 'message' => $reply]);
        }
    }

    // ---------------------------------------------------------
    // 🧠 1. GROQ INTELLIGENCE
    // ---------------------------------------------------------
    private function analyzeIntentWithGroq($message)
    {
        $apiKey = env('GROQ_API_KEY');
        $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

        if (!$apiKey) return ['error' => 'GROQ_API_KEY missing'];

        $systemPrompt = "
        You are a smart, friendly Query Builder for a Recipe App. 
        Your goal is to map User Input to the Database Schema EXACTLY.
        
        RULES:
        1. [CONVERSATION & SHORT RESPONSES - PRIORITY] 
           CHECK THIS FIRST. If the input is 'Okay', 'Yes', 'No', 'Hi', DO NOT SEARCH.
           - 'Hi', 'Hello', 'Who are you?' -> action='chit_chat', reply_text='Hi there! I am Chef Sage. Ready to cook? 🍳'.
           - 'Yes', 'Sure', 'Ready', 'I am ready' -> action='chit_chat', reply_text='Awesome! Tell me what ingredients you have or what you are craving. 🥘'.
           - 'No', 'Nope', 'Nah' -> action='chit_chat', reply_text='Okay, no problem! What would you like to eat instead? 🥗'.
           - 'Okay', 'Ok', 'Cool', 'Great', 'Thanks', 'Good' -> action='chit_chat', reply_text='You are very welcome! Let me know if you need anything else. 👩‍🍳'.
           - 'Bye', 'See you', 'See u', 'Cya' -> action='chit_chat', reply_text='See you later! 👋 Happy cooking!'.

        2. [SEARCH - EXACT MAPPING] Extract filters.
           - 'Pasta' -> filters: {'query': 'pasta'}
           - 'Fish meal' -> filters: {'query': 'fish'} (Clean the query)
        
        3. [SPECIFIC INFO & CONTEXT]
           - 'Steps?', 'How to make?', 'Steps for this' -> target_field: 'steps', use_context: true.
           - 'Time?', 'Duration?' -> target_field: 'time', use_context: true.
           - 'Ingredients?', 'What do I need?' -> target_field: 'ingredients', use_context: true.
           - 'Calories?', 'kcal?', 'How many calories' -> target_field: 'calories', use_context: true.
           - 'Difficulty?', 'How hard is it?', 'Level?' -> target_field: 'difficulty', use_context: true.

        4. [PHYSICAL STATE & WEATHER - PRIORITY]
           - 'Thirsty', 'Need a drink', 'I am thirty (treat as thirsty)' -> filters: {'category': 'drink'}.
           - 'It is hot', 'Hot outside' -> filters: {'temperature': 'cold'}.
           - 'It is cold', 'Winter' -> filters: {'temperature': 'hot'}.

        5. [SEARCH - DIRECT INGREDIENTS] 
           - 'What can I do if I have eggs and salt?' -> action='search_recipe', filters: {'ingredients': ['egg', 'salt']}
           - 'Chicken' -> action='search_recipe', filters: {'query': 'chicken'}

        6. [VAGUE HUNGER] 
           - 'I want to eat', 'I am hungry', 'Suggest something' -> action='search_recipe', filters: {'vague_hunger': true}.

        7. [PANTRY SUGGEST & STRICT MODE] 
           - 'What can I cook from my pantry?' -> action='pantry_suggest', filters: {'pantry_mode': 'suggest'}.
           - 'Can I make chicken based on pantry?' -> action='pantry_suggest', filters: {'pantry_mode': 'suggest', 'query': 'chicken'}.
           - 'Can I cook this?', 'Do I have ingredients?' -> action='pantry_suggest', filters: {'pantry_mode': 'suggest', 'use_context': true}.
           - '100%', 'Exactly', 'With no missing items', 'Fully match' -> action='pantry_suggest', filters: {'pantry_mode': 'suggest', 'strict_match': true}.
        
        8. [PANTRY RE-CHECK]
           If user implies they updated the pantry externally:
           - 'I added items', 'I updated my pantry', 'Check again', 'What about now?' -> action='pantry_suggest', filters: {'pantry_mode': 'suggest'}.

        9. [PANTRY CHECK/COUNT/LIST]
           - 'Do I have sugar?' -> action='get_pantry', filters: {'pantry_mode': 'check', 'query': 'sugar'}.
           - 'Show pantry', 'What do I have?' -> action='get_pantry', filters: {'pantry_mode': 'list'}.
        
        10. [SHOPPING LIST] 
           - 'Shopping list' -> action='get_shopping_list', filters: {'mode': 'list'}.
           - 'Check list for milk' -> action='get_shopping_list', filters: {'mode': 'check', 'query': 'milk'}.
        
        11. [FAVORITES] 'My favorites' -> action='get_favorites'.
        12. [HELP] 'Help' -> action='get_help'.
        13. [FULL INVENTORY] 'How many items in shopping and pantry?' -> action='get_full_inventory'.
        
        14. [WRITE PROTECTION] Add/Remove -> action='chit_chat', reply_text='Please use the Pantry/Shopping page to modify items directly. 📝'.
        
        15. [DISLIKE/CHANGE] 
            - 'Another one', 'Not that one', 'Change', 'Don't like this' -> action='search_recipe', filters: {'surprise_me': true, 'exclude_last': true}.
            
        16. [CONTEXT] 'This one', 'That recipe' -> action='search_recipe', filters: {'use_context': true}.

        Response JSON Structure:
        {
          \"action\": \"search_recipe\" | \"pantry_suggest\" | \"get_pantry\" | \"get_shopping_list\" | \"get_full_inventory\" | \"get_favorites\" | \"get_help\" | \"chit_chat\",
          \"filters\": {
             \"query\": \"...\",
             \"ingredients\": [],
             \"category\": \"...\",
             \"meal_type\": \"...\",
             \"temperature\": \"hot | cold\",
             \"pantry_mode\": \"...\",
             \"mode\": \"...\", 
             \"target_field\": \"...\",
             \"strict_match\": boolean,
             \"vague_hunger\": boolean,
             \"surprise_me\": boolean,
             \"exclude_last\": boolean,
             \"use_context\": boolean
          },
          \"reply_text\": \"...\"
        }
        ";

        try {
            $response = Http::withOptions(['verify' => false]) 
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($apiUrl, [
                    'model' => 'llama-3.1-8b-instant', 
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $message],
                    ],
                    'response_format' => ['type' => 'json_object'], 
                    'temperature' => 0.1 
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return json_decode($data['choices'][0]['message']['content'], true);
            }
            return ['error' => 'Groq API Error: ' . $response->status()];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // ---------------------------------------------------------
    // 🔍 2. SEARCH
    // ---------------------------------------------------------
    private function searchRecipe($user, $filters, $aiReply = null)
    {
        // 1. Time-Based Suggestion
        if (!empty($filters['vague_hunger'])) {
            $currentHour = (int) date('H'); 
            if ($currentHour >= 5 && $currentHour < 12) {
                $filters['meal_type'] = 'breakfast';
                $aiReply = "Good morning! ☀️ Since it's breakfast time, I recommend starting your day with this:";
            } elseif ($currentHour >= 12 && $currentHour < 17) {
                $filters['meal_type'] = 'lunch';
                $aiReply = "It's lunch time! 🕛 How about this meal to keep you going:";
            } else {
                $filters['meal_type'] = 'dinner';
                $aiReply = "Good evening! 🌙 For dinner tonight, I suggest you try this:";
            }
        }

        // 2. Context Handling
        if (!empty($filters['use_context']) && empty($filters['query']) && $user) {
            $cacheKey = 'last_suggestion_' . $user->id;
            if (Cache::has($cacheKey)) $filters['query'] = Cache::get($cacheKey);
            else return response()->json(['response_type' => 'text', 'message' => "I'm not sure which recipe you're referring to. Could you remind me? 🤔"]);
        }

        // 3. Surprise Me
        if (!empty($filters['surprise_me'])) {
            $query = Recipe::with('ingredients');
            if (!empty($filters['exclude_last']) && $user) {
                $cacheKey = 'last_suggestion_' . $user->id;
                if (Cache::has($cacheKey)) {
                    $lastTitle = Cache::get($cacheKey);
                    $query->where('title', '!=', $lastTitle);
                }
            }
            $randomRecipe = $query->inRandomOrder()->first();
            if ($randomRecipe) {
                if ($user) Cache::put('last_suggestion_' . $user->id, $randomRecipe->title, 600);
                return response()->json(['response_type' => 'recipe_card', 'message' => "How about something completely different? Surprise! 🎉", 'recipe' => $randomRecipe]);
            }
        }

        // 4. Build Query
        $query = Recipe::query();
        $hasFilters = false;

        if (!empty($filters['category'])) { $query->where('category', 'LIKE', '%' . $filters['category'] . '%'); $hasFilters = true; }
        if (!empty($filters['meal_type'])) { $query->where('meal_type', 'LIKE', '%' . $filters['meal_type'] . '%'); $hasFilters = true; }
        
        if (!empty($filters['temperature'])) {
            $query->where('temperature', $filters['temperature']);
            $hasFilters = true;
            if (!$aiReply) $aiReply = "Here is something " . $filters['temperature'] . " to enjoy! 🌡️";
        }

        if (!empty($filters['ingredients'])) {
            $ingredients = $filters['ingredients'];
            $query->where(function (Builder $q) use ($ingredients) {
                foreach ($ingredients as $ing) {
                    $cleanIng = trim($ing);
                    if (Str::endsWith($cleanIng, 's')) { $cleanIng = Str::singular($cleanIng); }
                    $q->whereHas('ingredients', function ($subQ) use ($cleanIng) { 
                        $subQ->where('name', 'LIKE', "%{$cleanIng}%"); 
                    });
                }
            });
            $hasFilters = true;
        }

        if (!empty($filters['query'])) {
            $word = trim($filters['query']);
            $cleanWord = (Str::endsWith($word, 's')) ? Str::singular($word) : $word;
            
            $query->where(function (Builder $q) use ($word, $cleanWord) {
                $q->where('title', 'LIKE', "%{$word}%")
                  ->orWhere('title', 'LIKE', "%{$cleanWord}%") 
                  ->orWhereHas('ingredients', function ($subQ) use ($cleanWord) { 
                      $subQ->where('name', 'LIKE', "%{$cleanWord}%"); 
                  });
            });
            $hasFilters = true;
        }

        if (!$hasFilters) {
             return response()->json(['response_type' => 'text', 'message' => "I'm ready to cook! 👨‍🍳 Tell me what ingredients you have (like 'eggs') or what you're craving."]);
        }

        $recipe = $query->with('ingredients')->inRandomOrder()->first();

        if (!$recipe && !empty($filters['category']) && (stripos($filters['category'], 'dessert') !== false || stripos($filters['category'], 'sweet') !== false)) {
            $recipe = Recipe::whereHas('ingredients', function ($q) {
                $q->where('name', 'LIKE', '%sugar%')->orWhere('name', 'LIKE', '%honey%');
            })->with('ingredients')->inRandomOrder()->first();
        }

        if (!$recipe) {
            $missingItem = $filters['query'] ?? implode(' & ', $filters['ingredients'] ?? []) ?? 'that';
            return response()->json(['response_type' => 'text', 'message' => "Hmm, I couldn't find a perfect match for '{$missingItem}'. 🧐 Try searching for a different ingredient!"]);
        }

        if ($user) Cache::put('last_suggestion_' . $user->id, $recipe->title, 600);

        // --- Target Field Response ---
        $targetField = $filters['target_field'] ?? null;
        if ($targetField) {
            if ($targetField === 'steps') {
                $steps = is_string($recipe->steps) ? json_decode($recipe->steps, true) : $recipe->steps;
                if (!is_array($steps)) $steps = [$recipe->steps];
                return response()->json(['response_type' => 'steps_list', 'message' => "Here are the steps to prepare {$recipe->title}: 👨‍🍳", 'steps' => $steps]);
            }
            $responseText = "";
            switch ($targetField) {
                case 'time': $responseText = "It takes about {$recipe->time} to prepare {$recipe->title}. ⏱️"; break;
                case 'calories': $responseText = "{$recipe->title} contains approximately {$recipe->calories} calories. 🔥"; break;
                case 'difficulty': $responseText = "{$recipe->title} is considered {$recipe->difficulty} to make. 💪"; break;
                case 'ingredients': $ings = $recipe->ingredients->pluck('name')->implode(', '); $responseText = "You will need: {$ings} for {$recipe->title}. 🛒"; break;
                default: return response()->json(['response_type' => 'recipe_card', 'message' => "Found {$recipe->title} for you.", 'recipe' => $recipe]);
            }
            return response()->json(['response_type' => 'text', 'message' => $responseText]);
        }

        return response()->json(['response_type' => 'recipe_card', 'message' => $aiReply ?? "Look what I found for you! 🍽️", 'recipe' => $recipe]);
    }

    // ---------------------------------------------------------
    // 🏠 3. PANTRY SUGGEST (LIST MODE)
    // ---------------------------------------------------------
    private function suggestFromPantry($user, $filters = [])
    {
        $mode = $filters['pantry_mode'] ?? 'suggest';
        $strictMatch = $filters['strict_match'] ?? false;
        
        $pantryItems = PantryItem::where('user_id', $user->id)
            ->pluck('item_name')
            ->map(fn($i) => strtolower(trim($i))) 
            ->toArray();

        $mustHave = null;
        if (!empty($filters['use_context']) && $user) {
            $mustHave = Cache::get('last_suggestion_' . $user->id);
        }
        if (empty($mustHave) && !empty($filters['query'])) {
            $word = trim($filters['query']);
            if (in_array(strtolower($word), ['this', 'it', 'the meal', 'this meal']) && $user) {
                $mustHave = Cache::get('last_suggestion_' . $user->id);
            } else {
                $mustHave = (Str::endsWith($word, 's')) ? Str::singular($word) : $word;
            }
        }

        if (empty($pantryItems) && !$mustHave) return response()->json(['response_type' => 'text', 'message' => "Your pantry looks a bit empty! 🛒 Try adding items."]);

        $recipes = Recipe::with('ingredients')->get();
        $suggestedRecipes = [];

        foreach ($recipes as $recipe) {
            $recipeIngredients = $recipe->ingredients->pluck('name')
                ->map(fn($i) => strtolower(trim($i)))
                ->toArray();
            
            // Check Must Have
            if ($mustHave) {
                if ($filters['use_context'] ?? false) {
                    if (stripos($recipe->title, $mustHave) === false) continue;
                } else {
                    $hasIngredient = false;
                    foreach ($recipeIngredients as $ing) { if (str_contains($ing, $mustHave)) { $hasIngredient = true; break; } }
                    if (!$hasIngredient && stripos($recipe->title, $mustHave) === false) continue;
                }
            }

            if (empty($recipeIngredients)) continue;
            
            // Check matches
            $existingCount = 0;
            foreach ($recipeIngredients as $rIng) {
                foreach ($pantryItems as $pItem) {
                    if (str_contains($rIng, $pItem) || str_contains($pItem, $rIng)) {
                        $existingCount++;
                        break; 
                    }
                }
            }

            $totalIngredients = count($recipeIngredients);
            $missingCount = $totalIngredients - $existingCount;
            
            // --- LOGIC: Strict vs Normal ---
            $isCandidate = false;
            
            if ($strictMatch) {
                if ($missingCount === 0) $isCandidate = true;
            } else {
                if ($missingCount <= 1) $isCandidate = true;
            }

            if ($isCandidate) {
                // Calculate missing items for this specific recipe
                $missingItems = array_values(array_diff($recipeIngredients, $pantryItems));
                
                $matchPercentage = ($totalIngredients > 0) ? round(($existingCount / $totalIngredients) * 100) : 0;
                $suggestedRecipes[] = [
                    'recipe' => $recipe, 
                    'match' => $matchPercentage,
                    'missing' => $missingItems // Store missing items here
                ];
            }
        }

        if ($mode === 'count_possible') {
            $count = count($suggestedRecipes);
            if ($count > 0) {
                Cache::put('last_suggestion_' . $user->id, $suggestedRecipes[0]['recipe']->title, 600);
                return response()->json(['response_type' => 'text', 'message' => "Good news! Based on your pantry, you can make roughly {$count} meals. 🍳"]);
            } else {
                return response()->json(['response_type' => 'text', 'message' => "I couldn't find a complete meal with your current items. Maybe check your shopping list? 📝"]);
            }
        }

        // --- RETURN LIST (With Missing Items Injection) ---
        if (!empty($suggestedRecipes)) {
            // Sort by match % desc
            usort($suggestedRecipes, fn($a, $b) => $b['match'] <=> $a['match']);
            
            // [FIXED] Transform array to include missing_items inside recipe object
            $finalRecipes = array_map(function($item) {
                $recipeData = $item['recipe']->toArray();
                $recipeData['missing_items'] = $item['missing']; // Inject missing items
                return $recipeData;
            }, $suggestedRecipes);

            // Cache the top one for context
            Cache::put('last_suggestion_' . $user->id, $finalRecipes[0]['title'], 600);

            $msg = $strictMatch 
                ? "Here are the meals you can make fully (100%)! 🎉" 
                : "Here is what you can cook based on your pantry: 🍳";

            return response()->json([
                'response_type' => 'recipes_list',
                'message' => $msg,
                'recipes' => array_slice($finalRecipes, 0, 10), // Limit to top 10
                'count' => count($finalRecipes)
            ]);
        }
        
        if ($mustHave) {
             return response()->json(['response_type' => 'text', 'message' => "I couldn't find a suitable recipe with '{$mustHave}' based on your pantry."]);
        }

        return response()->json(['response_type' => 'text', 'message' => "I couldn't find a good match for your pantry items right now (missing more than 1 item). 🤔"]);
    }

    // ---------------------------------------------------------
    // 📋 4. GET PANTRY
    // ---------------------------------------------------------
    private function getPantryContent($user, $filters)
    {
        $mode = $filters['pantry_mode'] ?? 'list'; 

        if ($mode === 'check' && !empty($filters['query'])) {
            $itemToCheck = trim($filters['query']); 
            if (Str::endsWith($itemToCheck, 's')) $itemToCheck = Str::singular($itemToCheck);

            $existsInPantry = PantryItem::where('user_id', $user->id)->where('item_name', 'LIKE', "%{$itemToCheck}%")->exists();
            if ($existsInPantry) return response()->json(['response_type' => 'text', 'message' => "Yes, you have '{$itemToCheck}' in your pantry! ✅"]);

            $existsInShopping = ShoppingItem::where('user_id', $user->id)->where('item_name', 'LIKE', "%{$itemToCheck}%")->exists();
            if ($existsInShopping) return response()->json(['response_type' => 'text', 'message' => "You don't have '{$itemToCheck}' in the pantry, but it IS on your shopping list! 📝"]);

            return response()->json(['response_type' => 'text', 'message' => "Nope, I couldn't find '{$itemToCheck}' anywhere. ❌"]);
        }

        $items = PantryItem::where('user_id', $user->id)->pluck('item_name')->toArray();
        $count = count($items);
        
        if ($mode === 'count') {
             return response()->json(['response_type' => 'text', 'message' => "You currently have {$count} items stored in your pantry."]);
        }

        return response()->json([
            'response_type' => 'pantry_list', 
            'message' => $count > 0 ? "Here is everything in your pantry:" : "Your pantry is empty.", 
            'pantry' => ['items' => $items, 'count' => $count]
        ]);
    }

    // ---------------------------------------------------------
    // 🛒 5. GET SHOPPING LIST
    // ---------------------------------------------------------
    private function getShoppingListContent($user, $filters)
    {
        $mode = $filters['mode'] ?? 'list';

        if ($mode === 'check' && !empty($filters['query'])) {
            $itemToCheck = Str::singular(trim($filters['query']));
            $exists = ShoppingItem::where('user_id', $user->id)->where('item_name', 'LIKE', "%{$itemToCheck}%")->exists();
            return response()->json(['response_type' => 'text', 'message' => $exists ? "Yes! '{$itemToCheck}' is already on your shopping list. 📝" : "No, '{$itemToCheck}' is not on the list yet."]);
        }

        $items = ShoppingItem::where('user_id', $user->id)->get();
        $count = $items->count();

        if ($mode === 'count') {
            return response()->json(['response_type' => 'text', 'message' => "You have {$count} items on your shopping list."]);
        }

        return response()->json([
            'response_type' => 'shopping_list', 
            'message' => $items->isNotEmpty() ? "Here is your shopping list:" : "Your shopping list is clean! 🛒", 
            'shopping' => ['items' => $items, 'count' => $count]
        ]);
    }

    // ---------------------------------------------------------
    // 📦 6. GET FULL INVENTORY
    // ---------------------------------------------------------
    private function getFullInventory($user)
    {
        $pantryItems = PantryItem::where('user_id', $user->id)->pluck('item_name')->toArray();
        $shoppingItems = ShoppingItem::where('user_id', $user->id)->get(); 

        return response()->json([
            'response_type' => 'full_inventory', 
            'message' => "Here is your full inventory summary: 📊",
            'pantry' => ['items' => $pantryItems, 'count' => count($pantryItems)],
            'shopping' => ['items' => $shoppingItems, 'count' => $shoppingItems->count()]
        ]);
    }

    // ---------------------------------------------------------
    // ❤️ 7. GET FAVORITES
    // ---------------------------------------------------------
    private function getFavorites($user)
    {
        $favorites = $user->favorites()->get();
        if ($favorites->isEmpty()) return response()->json(['response_type' => 'text', 'message' => "You haven't favorited any recipes yet. ❤️ Start exploring!"]);

        return response()->json([
            'response_type' => 'favorites_list', 
            'message' => "Here are your favorite recipes: ❤️", 
            'favorites' => ['recipes' => $favorites, 'count' => $favorites->count()]
        ]);
    }

    // ---------------------------------------------------------
    // 💡 8. HELP
    // ---------------------------------------------------------
    private function getHelp($filters)
    {
        return response()->json(['response_type' => 'text', 'message' => "👋 **How can I help?**\n\n1. **Recipes:** 'Make Burger', 'Steps for Pizza'.\n2. **Pantry:** 'What do I have?'.\n3. **Favorites:** 'Show my favorites'.\n4. **Shopping:** 'Check list'."]);
    }
}