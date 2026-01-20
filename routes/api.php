<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeSearchController;
use App\Http\Controllers\PantryController;
use App\Http\Controllers\PantryMatchController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LLMController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Admin\RecipeAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\IngredientAdminController;
use Illuminate\Http\Request;
use App\Http\Middleware\SetLanguage;



// Route::get('/test', function () {
//     return response()->json(['message' => 'Welcome to the API']);
// });

// Recipe Routes***( 2.Admint)***




// Recipe Routes***( 1. users)***
Route::get('/test', [RecipeController::class, 'test']);

Route::middleware(SetLanguage::class)->group(function () {
    //search
    Route::match(['get', 'post'], '/recipes/search', [RecipeController::class, 'search']);
    Route::get('/ingredients', [IngredientController::class, 'index']);

    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::get('/recipes/{id}', [RecipeController::class, 'show'])->whereNumber('id');
    Route::get('/recipes/slug/{slug}', [RecipeController::class, 'showrecipe']);
    Route::post('/recipes', [RecipeController::class, 'store']);

    //simple chat route 
    Route::post('/chat', [ChatController::class, 'handle']);
});



Route::middleware('auth:sanctum', SetLanguage::class)->group(function () {
    //pantry routes
    Route::get('/pantry', [PantryController::class, 'index']);
    Route::get('/pantry/lang', [PantryController::class, 'indexWithLang']);
    Route::post('/pantry', [PantryController::class, 'store']);
    Route::delete('/pantry/{id}', [PantryController::class, 'destroy']);
    Route::delete('/pantry/{id}/sync', [PantryController::class, 'destroyAndSync']);

    //favorite routes
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);

    Route::delete('/favorites/{recipe_id}', [FavoriteController::class, 'destroy']);
    //top-loved

    //shopping list routes
    Route::get('/shopping-list', [ShoppingListController::class, 'index']);
    Route::get('/shopping-list/lang', [ShoppingListController::class, 'indexWithLang']);
    Route::post('/shopping-list', [ShoppingListController::class, 'store']);
    Route::patch('/shopping-list/{id}', [ShoppingListController::class, 'toggle']);
    Route::delete('/shopping-list/{id}', [ShoppingListController::class, 'destroy']);

    //match-pantry
    Route::get('/recipes/match-pantry', [RecipeController::class, 'matchPantry']);
    // Route::match(['get', 'post'], '/recipes/match-pantry', [RecipeController::class, 'matchPantry']);
    //shopping  
    Route::post('/shopping/migrate', [ShoppingListController::class, 'migrate']);
    //surprise-me
    Route::get('/recipes/surprise-me', [RecipeController::class, 'surpriseMe']);
    //chat for model (Will be upgraded)
    Route::post('/ask', [LLMController::class, 'ask']);

    //logout route
    // Route::post('/logout', [AuthController::class, 'logout']);
    // //test auth route
    // Route::get('/me', function (Request $request) {
    //     return response()->json(['user' => $request->user()]);
    // });
});



Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});


/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', fn($request) => $request->user());
    Route::post('/refresh', [AuthController::class, 'refresh']);
});


/*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

Route::middleware(['test'])->get('/middleware-test', function () {
    return response()->json(['ok' => true]);
});
Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {

    // Route::get('/admin/recipes', [RecipeAdminController::class, 'index']);

});



Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {
        
        // 🔥 Dashboard Stats
        Route::get('/stats', [App\Http\Controllers\Admin\DashboardController::class, 'stats']);

        // ---------------------------------
        // 🍲 Admin Recipe Management
        // ---------------------------------
        
        // 1. هام جداً: الفلاتر لازم تكون قبل الـ ID
        Route::get('/recipes/options', [RecipeAdminController::class, 'getFilterOptions']);

        // 2. باقي المسارات
        Route::get('/recipes', [RecipeAdminController::class, 'index']); // دي اللي فيها الفلترة
        Route::post('/recipes', [RecipeAdminController::class, 'store']);
        Route::get('/recipes/{id}', [RecipeAdminController::class, 'show']);
        Route::put('/recipes/{id}', [RecipeAdminController::class, 'update']); // لاحظ إننا بنستخدم PUT للتعديل
        Route::delete('/recipes/{id}', [RecipeAdminController::class, 'destroy']);

        // ---------------------------------
        // 👤 Users CRUD
        // ---------------------------------
        Route::get('/users', [UserAdminController::class, 'index']);
        Route::get('/users/{id}', [UserAdminController::class, 'show']);
        Route::post('/users', [UserAdminController::class, 'store']);
        Route::put('/users/{id}', [UserAdminController::class, 'update']);
        Route::delete('/users/{id}', [UserAdminController::class, 'destroy']);

        // ---------------------------------
        // 🥕 Ingredients CRUD
        // ---------------------------------
        Route::get('/ingredients', [IngredientAdminController::class, 'index']);
        Route::get('/ingredients/{id}', [IngredientAdminController::class, 'show']);
        Route::post('/ingredients', [IngredientAdminController::class, 'store']);
        Route::put('/ingredients/{id}', [IngredientAdminController::class, 'update']);
        Route::delete('/ingredients/{id}', [IngredientAdminController::class, 'destroy']);
    });








Route::middleware(['auth:sanctum', 'CheckPass'])->group(function () {
    Route::post('/create', [CategoryController::class, 'create']);
    Route::post('/update', [CategoryController::class, 'update']);
    Route::post('/delete', [CategoryController::class, 'delete']);
    // Route::get('/getAll', [CategoryController::class, 'getAll']);
});

Route::middleware(['auth:sanctum', 'CheckPass', SetLanguage::class])->group(function () {
    Route::get('/getAll', [CategoryController::class, 'getAll']);
});


Route::middleware('auth:sanctum')->get('/test-auth', function (Request $request) {
    return response()->json(['user' => $request->user()]);
});

Route::post('/uploadimage', [ImageController::class, 'uploadimage']);

// Route::middleware(['checkpass', 'auth:sanctum'])->get('/getAll', [CategoryController::class, 'getAll']);
// Route::middleware(['jwt.verify', 'lang'])->group(function() {
//     Route::get('/getAll', [CategoryController::class, 'getAll']);
// });

// Route::post("login", "AuthController"AuthController@login);


// Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route::middleware(['verify.token'])->group(function () {
//     // Route::get('/user-data', [AuthController::class, 'index']);
//     Route::post('/logout', [AuthController::class, 'logout']);

// });

// Route::post('/logout', [AuthController::class, 'logout'])
//     ->middleware('auth:sanctum')
//     ->name('logout'); // <--- this is key

// Web logout for Blade
// Route::post('/logout', [AuthController::class, 'logout'])
//     ->middleware('auth')          // web session auth
//     ->name('logout');

// Route::post('/api/logout', [AuthController::class, 'apiLogout'])
//     ->middleware('auth:sanctum'); // token auth

// Route::post('/refresh', [AuthController::class, 'refresh'])
//     ->middleware('auth:sanctum'); // token auth

// Route::post('/refresh', [AuthController::class, 'refresh'])
//     ->middleware('jwt.auth'); // ✅ JWT middleware, not sanctum

// Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('verify.token');



Route::post('/smart-assistant', [App\Http\Controllers\SmartChatController::class, 'handle'])->middleware('auth:sanctum');
Route::get('/recipes/top-loved', [RecipeController::class, 'topLoved']);
