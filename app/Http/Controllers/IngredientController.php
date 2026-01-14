<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
      public function index()
    {
        // $ingredients = Ingredient::all();
        // return response()->json($ingredients);
        return Ingredient::orderBy('name')->pluck('name');
    }
}
 