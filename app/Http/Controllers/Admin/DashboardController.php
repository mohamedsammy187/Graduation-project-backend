<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_recipes' => Recipe::count(),
            'total_ingredients' => Ingredient::count(),
            'latest_recipes' => Recipe::latest()->take(5)->select('id', 'title_en', 'created_at')->get()
        ]);
    }
}