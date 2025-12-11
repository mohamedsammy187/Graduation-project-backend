<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;

// Route::get('/test', function () {
//     return response()->json(['message' => 'Welcome to the API']);
// });



// Recipe Routes***( 1 .first sprint)***
Route::get('/test', [RecipeController::class, 'test']);
Route::get('/recipes', [RecipeController::class, 'index']);
Route::get('/recipes/{id}', [RecipeController::class, 'show']);
Route::post('/recipes', [RecipeController::class, 'store']);


// Recipe Routes***( 2 . second sprint)***
Route::get('/ingredients', [IngredientController::class, 'index']);
Route::post('/recipes/search', [RecipeController::class, 'search']);


































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
