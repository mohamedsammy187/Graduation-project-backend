<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index(Request $request)
    {
        // اللغة الافتراضية إنجليزي
        $lang = $request->get('lang', 'en');

        // تحديد العمود حسب اللغة
        $column = $lang === 'ar' ? 'name_ar' : 'name_en';

        return Ingredient::orderBy($column)
            ->pluck($column);
    }
}