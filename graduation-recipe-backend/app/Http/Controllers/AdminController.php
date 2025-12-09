<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use PHPUnit\Metadata\Uses;

class AdminController extends Controller
{
    public function index()
    {
        $latestUsers = getLatest('*', 'users', 'id');
        // $latestItems = getLatest('*', 'categories', 'id');
        $latestItems = Category::orderBy('id', 'DESC')
            ->take(5)
            ->get();


        $usersCount = countItems('id', 'users');
        $productsCount = countItems('id', 'products');
        $categoriesCount = countItems('id', 'categories');

        return view('admin.dashboard', compact(
            'latestUsers',
            'latestItems',
            'usersCount',
            'productsCount',
            'categoriesCount'
        ));
    }
    // public function index()
    // {
    //     return view('admin.dashboard', [
    //         'productsCount' => Product::count(),
    //         'categoriesCount' => Category::count(),
    //         'usersCount' => User::count(),
    //     ]);
    // }

    public function productsTable()
    {
        $products = Product::all(); // ✅ send all rows
        return view('admin.products', ['products' => $products]);
    }
}
