<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

use App\Models\Review;
use Illuminate\Support\Facades\DB;

class FirstController extends Controller
{
    public function Master()
    {
        return view('layouts/master');
    }
     public function home()
    {
        return view('layouts/home');
    }


    public function CookWhatYouHave()
    {
        $products = Product::all();
        // Dummy suggestions (later you can generate dynamically)
        // في البداية نعرض الوجبات كلها
        $suggestedMeals = $this->getAllMeals();
        return view('layouts.cookwhatyouhave', compact('products', 'suggestedMeals'));
    }

    public function SearchMeals(Request $request)
    {
        $query = strtolower($request->input('ingredients')); // المكونات اللي كتبها المستخدم
        $userIngredients = array_map('trim', explode(',', $query));

        $allMeals = $this->getAllMeals();

        // فلترة الوجبات اللي تحتوي على أي مكون من اللي كتبهم المستخدم
        $suggestedMeals = array_filter($allMeals, function ($meal) use ($userIngredients) {
            foreach ($userIngredients as $ingredient) {
                if (in_array(ucfirst($ingredient), $meal['ingredients'])) {
                    return true;
                }
            }
            return false;
        });

        return view('layouts.cookwhatyouhave', compact('suggestedMeals'))
            ->with('searchQuery', $request->ingredients);
    }

    // ✅ دالة ترجع كل الوجبات
    private function getAllMeals()
    {
        return [
            [
                'title' => 'Fresh Veggie Salad',
                'description' => 'A healthy salad made with seasonal veggies.',
                'ingredients' => ['Tomato', 'Cucumber', 'Lettuce'],
                'image' => 'img/meals/salad.jpg',
                'link' => '/recipes/salad'
            ],
            [
                'title' => 'Creamy Pasta',
                'description' => 'Delicious pasta with creamy sauce & cheese.',
                'ingredients' => ['Pasta', 'Milk', 'Cheese'],
                'image' => 'img/meals/pasta.jpg',
                'link' => '/recipes/pasta'
            ],
            [
                'title' => 'Grilled Chicken',
                'description' => 'Juicy grilled chicken with herbs.',
                'ingredients' => ['Chicken', 'Garlic', 'Olive Oil'],
                'image' => 'img/meals/chicken.jpg',
                'link' => '/recipes/chicken'
            ]
        ];
    }



    // صفحة الكاتيجوريز
    public function GetCatProducts()
    {
        $categories = Category::all();
        $products   = Product::all();

        // if (Auth::check()) {
        //     $categories = Category::all();
        //     $products   = Product::all();
        // } else {
        //     $categories = Category::take(3)->get();
        // }

        return view('category', compact('categories'));
    }

    // صفحة المنتجات بالفلترة على الكاتيجوري
    public function GetShopProducts($catid = 0)
    {
        $categories = Category::all();

        if ($catid && $catid > 0) {
            // نستخدم paginate بدل get
            $products = Product::where('cat_id', $catid)->paginate(8);
        } else {
            $products = Product::paginate(8);
        }

        return view('product', compact('products', 'categories', 'catid'));
    }


    public function GetShopDetails($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        $categories = DB::table('categories')->get();

        return view('layouts.shopdetail', [
            'product' => $product,
            'categories' => $categories
        ]);
    }
    public function Reviews()
    {
        // هات كل الريفيوز من الجدول
        $reviews = Review::all();

        // ابعته للـ Blade
        return view('review', compact('reviews'));
    }
    public function counter(){
        return view('livewire.counter');


    }
}
