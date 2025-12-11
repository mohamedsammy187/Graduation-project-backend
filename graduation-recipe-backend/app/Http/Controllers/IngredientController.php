<?php

namespace App\Http\Controllers;

use App\Models\ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
      public function index()
    {
        // $ingredients = ingredient::all();
        // return response()->json($ingredients);
        return ingredient::orderBy('name')->pluck('name');
    }
}
 