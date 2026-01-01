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
use Illuminate\Http\Request;

// Route::get('/test', function () {
//     return response()->json(['message' => 'Welcome to the API']);
// });

// Recipe Routes***( 2 . second sprint)***


// Recipe Routes***( 1 .first sprint)***
Route::get('/test', [RecipeController::class, 'test']);
//search
Route::match(['get', 'post'], '/recipes/search', [RecipeSearchController::class, 'search']);
Route::get('/ingredients', [IngredientController::class, 'index']);

Route::get('/recipes', [RecipeController::class, 'index']);
Route::get('/recipes/{id}', [RecipeController::class, 'show'])->whereNumber('id');
Route::get('/recipes/slug/{slug}', [RecipeController::class, 'showrecipe']);
Route::post('/recipes', [RecipeController::class, 'store']);

//simple chat route 
Route::post('/chat', [ChatController::class, 'handle']);

//chat for model (Will be upgraded)
Route::post('/ask', [LLMController::class, 'ask']);

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //pantry routes
    Route::get('/pantry', [PantryController::class, 'index']);
    Route::post('/pantry', [PantryController::class, 'store']);
    Route::delete('/pantry/{id}', [PantryController::class, 'destroy']);

    //favorite routes
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{recipe_id}', [FavoriteController::class, 'destroy']);

    //shopping list routes
    Route::get('/shopping-list', [ShoppingListController::class, 'index']);
    Route::post('/shopping-list', [ShoppingListController::class, 'store']);
    Route::patch('/shopping-list/{id}', [ShoppingListController::class, 'toggle']);
    Route::delete('/shopping-list/{id}', [ShoppingListController::class, 'destroy']);
    
    //match-pantry
    Route::get('/recipes/match-pantry', [RecipeController::class, 'm    atchPantry']);
    // Route::match(['get', 'post'], '/recipes/match-pantry', [RecipeController::class, 'matchPantry']);
    //shopping  
    Route::post('/shopping/migrate', [ShoppingListController::class, 'migrate']);


    //logout route
    Route::post('/logout', [AuthController::class, 'logout']);
    //test auth route
    Route::get('/me', function (Request $request) {
        return response()->json(['user' => $request->user()]);
    });
});

































Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});


Route::middleware(['auth:sanctum', 'CheckPass'])->group(function () {
    Route::post('/create', [CategoryController::class, 'create']);
    Route::post('/update', [CategoryController::class, 'update']);
    Route::post('/delete', [CategoryController::class, 'delete']);
    // Route::get('/getAll', [CategoryController::class, 'getAll']);
});

Route::middleware(['auth:sanctum', 'CheckPass', 'lang'])->group(function () {
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
