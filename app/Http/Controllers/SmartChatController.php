<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

// -----------------------------------------------------------------------------
// 📦 MODELS IMPORT
// -----------------------------------------------------------------------------
use App\Models\Recipe;
use App\Models\PantryItem;
use App\Models\ShoppingItem;
use App\Models\User;
use App\Models\Category; // Critical for the Category Bridge

/**
 * Class SmartChatController
 * * The central brain for the Chef Sage AI assistant.
 * This controller handles Natural Language Understanding (NLU) via Groq,
 * translates intents into Database Queries, and formats responses for the UI.
 */
class SmartChatController extends Controller
{
    /**
     * @var float Temperature for the AI model (Creativity vs Precision)
     */
    private $aiTemperature = 0.1;

    /**
     * @var string The specific model identifier for Groq
     */
    private $aiModel = 'llama-3.1-8b-instant';

    // =========================================================================
    // 🚀 1. MASTER REQUEST HANDLER
    // =========================================================================

    /**
     * Main entry point. Receives the user's prompt and orchestrates the response.
     * * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        // 1.1 Setup & Language Detection
        $user = $request->user();
        $userMessage = $request->input('prompt');
        // Prioritize explicit lang param, fallback to header, default to 'en'
        $lang = $request->input('lang', $request->header('Accept-Language', 'en'));

        // 1.2 Input Validation
        if (!$userMessage || trim($userMessage) === '') {
            return response()->json([
                'response_type' => 'text',
                'message' => ($lang === 'ar' ? 'من فضلك اكتب شيئاً لأبحث عنه.' : 'Please provide a prompt.')
            ], 400);
        }

        // 1.3 Intent Analysis (The AI Step)
        // We send the raw text to Groq to get a structured JSON command object
        $aiAnalysis = $this->analyzeIntentWithGroq($userMessage, $lang);

        // 1.4 Critical Failure Check
        if (isset($aiAnalysis['error'])) {
            Log::error("SmartChat Groq API Failure", ['error' => $aiAnalysis['error']]);
            return response()->json([
                'response_type' => 'text',
                'message' => ($lang === 'ar' 
                    ? 'النظام مشغول حالياً. يرجى المحاولة لاحقاً.' 
                    : 'System is currently unavailable. Please try again later.')
            ], 500);
        }

        // 1.5 Routing Logic
        try {
            $action = $aiAnalysis['action'] ?? 'chit_chat';
            $filters = $aiAnalysis['filters'] ?? [];
            $replyText = $aiAnalysis['reply_text'] ?? null;

            // Log the decision for debugging
            Log::info("SmartChat Decision", [
                'user' => $user ? $user->id : 'guest',
                'action' => $action,
                'filters' => $filters
            ]);

            switch ($action) {
                case 'search_recipe':
                    return $this->handleRecipeSearchStrategy($user, $filters, $replyText, $lang);
                
                case 'pantry_suggest':
                    return $this->handlePantrySuggestionStrategy($user, $filters, $lang);
                
                case 'get_pantry':
                    return $this->handleGetPantryStrategy($user, $filters, $lang);
                
                case 'get_shopping_list':
                    return $this->handleGetShoppingListStrategy($user, $filters, $lang);
                
                case 'get_full_inventory':
                    return $this->handleFullInventoryStrategy($user, $lang);
                
                case 'get_favorites':
                    return $this->handleFavoritesStrategy($user, $lang);
                
                case 'get_help':
                    return $this->handleHelpStrategy($lang);
                
                case 'chit_chat':
                default:
                    return $this->handleChitChatStrategy($replyText, $lang);
            }

        } catch (\Throwable $e) {
            // Global Exception Handler to prevent 500 White Screen
            Log::critical("SmartChat Logic Crash", [
                'msg' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'response_type' => 'text',
                'message' => ($lang === 'ar' 
                    ? "عذراً، حدث خطأ فني أثناء معالجة طلبك." 
                    : "Sorry, a technical error occurred while processing your request.")
            ], 500);
        }
    }

    // =========================================================================
    // 🧠 2. INTELLIGENCE LAYER (GROQ API)
    // =========================================================================

    /**
     * Communicates with the LLM to convert natural language into structured JSON.
     */
    private function analyzeIntentWithGroq($message, $lang)
    {
        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) return ['error' => 'API Key Config Missing'];

        $promptLang = ($lang === 'ar' ? 'Arabic' : 'English');

        $systemPrompt = <<<EOT
        You are 'Chef Sage', a database query translator for a Recipe Application.
        Target Language: {$promptLang}.
        
        YOUR JOB: Extract search filters from the user's input.

        DATABASE CONTEXT:
        - Categories: 'Main Course', 'Dessert', 'Drink', 'Snack', 'Salad'.
        - Synonyms: 
            'Sweet', 'Cake', 'Treat' -> 'Dessert'
            'Thirsty', 'Juice', 'Soda' -> 'Drink'
            'Morning', 'Breakfast' -> meal_type='Breakfast'
        
        INTENT RULES:
        1. 'Hi', 'Hello' -> action='chit_chat', reply_text='Hello! Ready to cook?'.
        2. 'Pasta' -> action='search_recipe', filters: {'query': 'pasta'}.
        3. 'I want juice' -> action='search_recipe', filters: {'category': 'Drink'}.
        4. 'Something sweet' -> action='search_recipe', filters: {'category': 'Dessert'}.
        5. 'What can I cook?' -> action='pantry_suggest', filters: {'pantry_mode': 'suggest'}.

        Response JSON Structure:
        {
          "action": "search_recipe" | "pantry_suggest" | "get_pantry" | "get_shopping_list" | "get_full_inventory" | "get_favorites" | "get_help" | "chit_chat",
          "filters": {
             "query": "string or null",
             "ingredients": ["array", "of", "strings"],
             "category": "string or null",
             "meal_type": "string or null",
             "target_field": "string or null",
             "vague_hunger": boolean,
             "surprise_me": boolean,
             "use_context": boolean
          },
          "reply_text": "Friendly response string"
        }
EOT;

        try {
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $this->aiModel,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $message],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => $this->aiTemperature
                ]);

            if ($response->successful()) {
                return json_decode($response->json()['choices'][0]['message']['content'], true);
            }
            return ['error' => 'Groq API Status: ' . $response->status()];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // =========================================================================
    // 🔍 3. RECIPE SEARCH STRATEGY (THE CORE LOGIC)
    // =========================================================================

    /**
     * Executes the recipe search with multiple fallback layers.
     * Layer 1: Strict Search (Category ID + Ingredients).
     * Layer 2: Text Fallback (Search category name in Title).
     */
    private function handleRecipeSearchStrategy($user, $filters, $aiReply, $lang)
    {
        // 3.1 Normalize Inputs (The Synonym Bridge)
        $filters = $this->normalizeFilters($filters);

        // 3.2 Context Retrieval (Did user say "steps for that"?)
        if (!empty($filters['use_context']) && empty($filters['query']) && $user) {
            $lastTitle = Cache::get('last_suggestion_' . $user->id);
            if ($lastTitle) {
                $filters['query'] = $lastTitle;
            } else {
                return response()->json([
                    'response_type' => 'text', 
                    'message' => ($lang === 'ar' ? "عن أي وصفة تتحدث؟ 🤔" : "Which recipe are you referring to? 🤔")
                ]);
            }
        }

        // 3.3 Time-Based Suggestions (Vague Hunger)
        if (!empty($filters['vague_hunger'])) {
            $sug = $this->getTimeBasedSuggestion($lang);
            $filters['meal_type'] = $sug['meal_type'];
            if (!$aiReply) $aiReply = $sug['message'];
        }

        // 3.4 Surprise Me
        if (!empty($filters['surprise_me'])) {
            return $this->executeSurpriseSearch($user, $lang);
        }

        // ---------------------------------------------------------
        // 🚀 PHASE 1: EXECUTE HYBRID SEARCH
        // ---------------------------------------------------------
        $recipe = $this->executeHybridSearch($filters);

        // ---------------------------------------------------------
        // ⚠️ PHASE 2: CATEGORY TEXT FALLBACK
        // If searching for "Category: Drink" returned 0 results (maybe mapping issue),
        // we try searching for "Drink" inside the Title/Description.
        // ---------------------------------------------------------
        if (!$recipe && !empty($filters['category'])) {
            Log::info("Strict Category Search failed. Attempting text fallback for: " . $filters['category']);
            
            // Move category to query to force a text-based search
            $filters['query'] = $filters['category'];
            unset($filters['category']); // Remove strict filter
            
            $recipe = $this->executeHybridSearch($filters);
        }

        // 3.5 No Results Handling
        if (!$recipe) {
            $term = $filters['query'] ?? ($filters['category'] ?? 'your request');
            return response()->json([
                'response_type' => 'text',
                'message' => ($lang === 'ar' 
                    ? "لم أجد وصفة تطابق '{$term}'. 🧐 جرب البحث عن شيء آخر!" 
                    : "I couldn't find a match for '{$term}'. 🧐 Try searching for something else!")
            ]);
        }

        // 3.6 Save Context
        if ($user) {
            Cache::put('last_suggestion_' . $user->id, $recipe->title_en, 600);
        }

        // 3.7 Handle Specific Field Requests (Steps, Calories)
        if (!empty($filters['target_field'])) {
            return $this->formatSpecificFieldResponse($recipe, $filters['target_field'], $lang);
        }

        // 3.8 Return Full Recipe Card
        return response()->json([
            'response_type' => 'recipe_card',
            'message' => $aiReply ?? ($lang === 'ar' ? "وجدت لك هذه الوصفة! 🍽️" : "I found this for you! 🍽️"),
            'recipe' => $recipe
        ]);
    }

    /**
     * THE HYBRID QUERY BUILDER
     * Connects Categories, Ingredients, and Text search safely.
     */
    private function executeHybridSearch($filters)
    {
        $query = Recipe::query();

        // A. CATEGORY LOGIC (The Manual Bridge)
        // -------------------------------------------------
        if (!empty($filters['category'])) {
            $catName = $filters['category'];
            
            // Step 1: Look up the ID in the 'categories' table
            // We search both English and Arabic names
            $categoryIds = Category::where('name_en', 'LIKE', "%{$catName}%")
                                   ->orWhere('name_ar', 'LIKE', "%{$catName}%")
                                   ->pluck('id')
                                   ->toArray();

            if (!empty($categoryIds)) {
                // If we found IDs (e.g., Drink = 3), search using category_id
                $query->whereIn('category_id', $categoryIds);
            } else {
                // Fallback: If Category table doesn't have it, check the text column in recipes table
                $query->where('category', 'LIKE', "%{$catName}%");
            }
        }

        // B. MEAL TYPE LOGIC
        // -------------------------------------------------
        if (!empty($filters['meal_type'])) {
            $query->where('meal_type', 'LIKE', '%' . $filters['meal_type'] . '%');
        }

        // C. INGREDIENTS LOGIC (Dual Search)
        // -------------------------------------------------
        if (!empty($filters['ingredients'])) {
            foreach ($filters['ingredients'] as $ing) {
                $query->where(function(Builder $q) use ($ing) {
                    // 1. Search JSON Column (Safe fallback)
                    $q->where('ingredients', 'LIKE', "%{$ing}%");
                    
                    // 2. Search Relationship (If pivot exists)
                    // We wrap this in a check implicitly via whereHas logic
                    $q->orWhereHas('ingredients', function($relQ) use ($ing) {
                        $relQ->where('name_en', 'LIKE', "%{$ing}%")
                             ->orWhere('name_ar', 'LIKE', "%{$ing}%");
                    });
                });
            }
        }

        // D. TEXT SEARCH (Universal)
        // -------------------------------------------------
        if (!empty($filters['query'])) {
            $word = $filters['query'];
            $query->where(function(Builder $q) use ($word) {
                $q->where('title_en', 'LIKE', "%{$word}%")
                  ->orWhere('title_ar', 'LIKE', "%{$word}%")
                  ->orWhere('description_en', 'LIKE', "%{$word}%")
                  ->orWhere('description_ar', 'LIKE', "%{$word}%");
            });
        }

        // Eager load ingredients to prevent N+1 queries later
        return $query->with('ingredients')->inRandomOrder()->first();
    }

    /**
     * Normalizes User Filters (Synonym Bridge)
     */
    private function normalizeFilters($filters)
    {
        if (!empty($filters['category'])) {
            $c = strtolower($filters['category']);
            
            // Map common words to DB Category Names
            if (Str::contains($c, ['sweet', 'cake', 'cookie', 'dessert', 'chocolate'])) {
                $filters['category'] = 'Dessert';
            }
            if (Str::contains($c, ['drink', 'juice', 'soda', 'beverage', 'water', 'thirsty'])) {
                $filters['category'] = 'Drink'; // Note: Your DB uses singular "Drink" (ID 3)
            }
            if (Str::contains($c, ['snack', 'bite', 'chips'])) {
                $filters['category'] = 'Snack';
            }
        }
        
        // Normalize Ingredients (remove plurals)
        if (!empty($filters['ingredients'])) {
            $filters['ingredients'] = array_map(function($i) {
                return Str::singular(trim($i));
            }, $filters['ingredients']);
        }

        return $filters;
    }

    // =========================================================================
    // 🏠 4. PANTRY & LIST STRATEGIES
    // =========================================================================

    /**
     * Interfaces with RecipeController to find matching recipes.
     */
    private function handlePantrySuggestionStrategy($user, $filters, $lang)
    {
        if (!$user) return $this->sendLoginRequiredResponse($lang);

        try {
            // Instantiate existing controller to reuse logic
            $recipeController = new \App\Http\Controllers\RecipeController();
            
            // Mock a request object
            $req = new Request();
            $req->setUserResolver(fn() => $user);
            $req->merge([
                'lang' => $lang,
                'allow_missing_one' => 'true' // Force lenient matching
            ]);
            
            // Set locale for the app
            app()->setLocale($lang);

            // Call the matching logic
            $res = $recipeController->matchPantry($req);
            $data = $res->getData(true);
            $recipes = $data['data'] ?? [];

            if (empty($recipes)) {
                return response()->json([
                    'response_type' => 'text',
                    'message' => ($lang === 'ar' 
                        ? "مكونات المخزن الحالية لا تكفي لطبخ وجبة كاملة. 🛒 حاول إضافة بعض الأساسيات!" 
                        : "Your current pantry items aren't enough for a full meal. 🛒 Try adding some basics!")
                ]);
            }

            // Cache context
            if (isset($recipes[0]['title'])) {
                Cache::put('last_suggestion_' . $user->id, $recipes[0]['title'], 600);
            }

            // Format for Frontend (Add missing_items tags)
            $recipes = array_map(function($r) {
                $r['missing_items'] = isset($r['missing_ingredients']) 
                    ? collect($r['missing_ingredients'])->pluck('name')->toArray() 
                    : [];
                return $r;
            }, $recipes);

            return response()->json([
                'response_type' => 'recipes_list',
                'message' => ($lang === 'ar' ? "إليك ما يمكنك طبخه: 🍳" : "Here is what you can cook: 🍳"),
                'recipes' => array_slice($recipes, 0, 10),
                'count' => count($recipes)
            ]);

        } catch (\Exception $e) {
            Log::error("Pantry Logic Error: " . $e->getMessage());
            return response()->json([
                'response_type' => 'text',
                'message' => ($lang === 'ar' ? "حدث خطأ أثناء فحص المخزن." : "An error occurred checking the pantry.")
            ]);
        }
    }

    private function handleGetPantryStrategy($user, $filters, $lang)
    {
        if (!$user) return $this->sendLoginRequiredResponse($lang);

        // Specific Item Check Mode
        if (!empty($filters['query'])) {
            $item = Str::singular(trim($filters['query']));
            
            $exists = PantryItem::where('user_id', $user->id)
                ->where('item_name', 'LIKE', "%{$item}%")
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'response_type' => 'text', 
                    'message' => ($lang === 'ar' ? "نعم، '{$item}' موجود في المخزن! ✅" : "Yes, you have '{$item}' in your pantry! ✅")
                ]);
            }
            
            return response()->json([
                'response_type' => 'text', 
                'message' => ($lang === 'ar' ? "لا، '{$item}' غير موجود. ❌" : "No, '{$item}' is missing. ❌")
            ]);
        }

        // List Mode
        $items = PantryItem::where('user_id', $user->id)->pluck('item_name')->toArray();
        $count = count($items);

        return response()->json([
            'response_type' => 'pantry_list',
            'message' => $count > 0 
                ? ($lang === 'ar' ? "إليك محتويات مخزنك:" : "Here is your Pantry:") 
                : ($lang === 'ar' ? "مخزنك فارغ." : "Your pantry is empty."),
            'pantry' => ['items' => $items, 'count' => $count]
        ]);
    }

    private function handleGetShoppingListStrategy($user, $filters, $lang)
    {
        if (!$user) return $this->sendLoginRequiredResponse($lang);

        $items = ShoppingItem::where('user_id', $user->id)->get();
        return response()->json([
            'response_type' => 'shopping_list',
            'message' => $items->count() > 0 
                ? ($lang === 'ar' ? "قائمة التسوق الخاصة بك:" : "Your Shopping List:") 
                : ($lang === 'ar' ? "قائمة التسوق فارغة. 🛒" : "Shopping list is empty. 🛒"),
            'shopping' => ['items' => $items, 'count' => $items->count()]
        ]);
    }

    private function handleFullInventoryStrategy($user, $lang)
    {
        if (!$user) return $this->sendLoginRequiredResponse($lang);

        $pantryItems = PantryItem::where('user_id', $user->id)->pluck('item_name')->toArray();
        $shoppingItems = ShoppingItem::where('user_id', $user->id)->get();

        return response()->json([
            'response_type' => 'full_inventory',
            'message' => ($lang === 'ar' ? "الملخص الكامل:" : "Full Inventory Summary:"),
            'pantry' => ['items' => $pantryItems, 'count' => count($pantryItems)],
            'shopping' => ['items' => $shoppingItems, 'count' => $shoppingItems->count()]
        ]);
    }

    private function handleFavoritesStrategy($user, $lang)
    {
        if (!$user) return $this->sendLoginRequiredResponse($lang);

        $favs = $user->favorites()->get();
        return response()->json([
            'response_type' => 'favorites_list',
            'message' => $favs->count() > 0 
                ? ($lang === 'ar' ? "وصفاتك المفضلة: ❤️" : "Your Favorites: ❤️") 
                : ($lang === 'ar' ? "لا توجد مفضلات بعد." : "No favorites yet."),
            'favorites' => ['recipes' => $favs, 'count' => $favs->count()]
        ]);
    }

    // =========================================================================
    // 🛠️ 5. HELPER METHODS
    // =========================================================================

    /**
     * Safely formats specific fields (like Steps) which might be JSON or String.
     */
    private function formatSpecificFieldResponse($recipe, $field, $lang)
    {
        $title = $lang === 'ar' ? ($recipe->title_ar ?? $recipe->title_en) : $recipe->title_en;

        switch ($field) {
            case 'steps':
                $stepsData = $recipe->steps;
                // Decode if string
                if (is_string($stepsData)) {
                    $decoded = json_decode($stepsData, true);
                    $stepsData = $decoded ?: [$stepsData];
                }
                
                // Extract Language
                $finalSteps = [];
                if (is_array($stepsData)) {
                    if ($lang === 'ar' && isset($stepsData['ar'])) $finalSteps = $stepsData['ar'];
                    elseif (isset($stepsData['en'])) $finalSteps = $stepsData['en'];
                    else $finalSteps = $stepsData;
                } else {
                    $finalSteps = [$stepsData];
                }
                
                // Ensure Array
                if (!is_array($finalSteps)) $finalSteps = [$finalSteps];

                return response()->json([
                    'response_type' => 'steps_list',
                    'message' => ($lang === 'ar' ? "خطوات تحضير {$title}:" : "Steps for {$title}:"),
                    'steps' => $finalSteps
                ]);

            case 'time':
                return response()->json(['response_type' => 'text', 'message' => "Time required: {$recipe->time}."]);

            case 'calories':
                return response()->json(['response_type' => 'text', 'message' => "Calories: {$recipe->calories}."]);

            case 'ingredients':
                 $ingText = "Ingredients listed below.";
                 // Try to get relation data first
                 if ($recipe->relationLoaded('ingredients') && $recipe->ingredients->isNotEmpty()) {
                     $ingText = $recipe->ingredients->pluck('name_en')->implode(', ');
                 }
                return response()->json(['response_type' => 'text', 'message' => "Ingredients: {$ingText}"]);

            default:
                return response()->json([
                    'response_type' => 'recipe_card', 
                    'message' => "Here is {$title}:", 
                    'recipe' => $recipe
                ]);
        }
    }

    private function executeSurpriseSearch($user, $lang)
    {
        $query = Recipe::with('ingredients');
        if ($user) {
            $last = Cache::get('last_suggestion_' . $user->id);
            if ($last) $query->where('title_en', '!=', $last);
        }
        $recipe = $query->inRandomOrder()->first();
        
        return response()->json([
            'response_type' => 'recipe_card',
            'message' => ($lang === 'ar' ? "مفاجأة! 🎉" : "Surprise! 🎉"),
            'recipe' => $recipe
        ]);
    }

    private function getTimeBasedSuggestion($lang)
    {
        $h = Carbon::now()->hour;
        if ($h >= 5 && $h < 11) return ['meal_type' => 'breakfast', 'message' => ($lang==='ar' ? 'فطور:' : 'Breakfast idea:')];
        if ($h >= 11 && $h < 17) return ['meal_type' => 'lunch', 'message' => ($lang==='ar' ? 'غداء:' : 'Lunch idea:')];
        return ['meal_type' => 'dinner', 'message' => ($lang==='ar' ? 'عشاء:' : 'Dinner idea:')];
    }

    private function handleChitChatStrategy($reply, $lang)
    {
        $default = $lang === 'ar' ? "مرحباً! أنا جاهز للمساعدة." : "Hi! I'm ready to help you cook.";
        return response()->json(['response_type' => 'text', 'message' => $reply ?? $default]);
    }

    private function handleHelpStrategy($lang)
    {
        return response()->json([
            'response_type' => 'text',
            'message' => ($lang === 'ar' 
                ? "💡 **أوامر المساعد:**\n1. البحث: 'دجاج', 'بيتزا'\n2. التصنيفات: 'عصير', 'حلويات'\n3. المخزن: 'ماذا أطبخ؟'" 
                : "💡 **Commands:**\n1. Search: 'Chicken', 'Pizza'\n2. Category: 'Juice', 'Sweet'\n3. Pantry: 'What can I cook?'")
        ]);
    }

    private function sendLoginRequiredResponse($lang)
    {
        return response()->json([
            'response_type' => 'text', 
            'message' => ($lang === 'ar' ? 'يرجى تسجيل الدخول أولاً. 🔐' : 'Please login first. 🔐')
        ]);
    }
}