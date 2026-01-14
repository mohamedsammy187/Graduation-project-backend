<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiHandler;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiHandler;

    public function create(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'imagepath' => 'required|image',
            'description' => 'nullable|string',
            'ordering' => 'nullable|integer',
            'Visibility' => 'nullable|boolean',
            'Allow_Comments' => 'nullable|boolean',
            'Allow_Ads' => 'nullable|boolean',
        ]);

        $category = Category::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'imagepath' => $request->imagepath,
            'description' => $request->description,
            'Ordering' => $request->Ordering ?? 0,
            'Visibility' => $request->Visibility ?? 1,
            'Allow_Comments' => $request->Allow_Comments ?? 1,
            'Allow_Ads' => $request->Allow_Ads ?? 1,
        ]);

        return response()->json(['msg' => 'added', 'data' => $category]);
    }


    public function showCreateForm()
    {
        $categories = Category::all();
        return view('admin.categories.add', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'imagepath' => 'required|image',
            'description' => 'nullable|string',
            'Ordering' => 'nullable|integer',
            'Visibility' => 'nullable|boolean',
            'Allow_Comments' => 'nullable|boolean',
            'Allow_Ads' => 'nullable|boolean',
        ]);

        $category = new Category();
        $category->name_en = $request->name_en;
        $category->name_ar = $request->name_ar;
        $category->description = $request->description;
        $category->Ordering = $request->Ordering ?? 0;
        $category->Visibility = $request->Visibility ?? 1;
        $category->Allow_Comments = $request->Allow_Comments ?? 1;
        $category->Allow_Ads = $request->Allow_Ads ?? 1;

        if ($request->hasFile('imagepath')) {
            $imageName = time() . '.' . $request->imagepath->extension();
            $request->imagepath->move(public_path('asset/img/categories'), $imageName);
            $category->imagepath = 'asset/img/categories/' . $imageName;
        }

        $category->save();

        // ✅ Redirect to index page with success message
        return redirect()->route('admin.categories')->with('success', 'Category added successfully!');
    }





    public function getAll(Request $request)
    {
        // Get locale from header, query, or fallback
        $locale = $request->header('lang', app()->getLocale() ?? 'en');
        $locale = in_array($locale, ['en', 'ar']) ? $locale : 'en';

        $products = Product::select('id', 'name_' . $locale)->get();

        return response()->json([
            'msg' => 'Get all products',
            'locale' => $locale,
            'data' => $products
        ]);
    }

    //show edit form
    public function edit($id)
    {
        $category = Category::findorfail($id);
        return view('admin.categories.edit', compact('category'));
    }

    //handle update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'required',
            'name_en' => 'required',
            'description' => 'nullable|string',
            'Ordering' => 'nullable|integer',
            'Visibility' => 'nullable|boolean',
            'Allow_Comments' => 'nullable|boolean',
            'Allow_Ads' => 'nullable|boolean',
            'imagepath' => 'nullable|image|max:2048',
        ]);
        $category = Category::findorfail($id);
        $category->name_en = $request->name_en;
        $category->name_ar = $request->name_ar;
        $category->description = $request->description;
        $category->Ordering = $request->Ordering ?? 0;
        $category->Visibility = $request->Visibility ?? 1;
        $category->Allow_Comments = $request->Allow_Comments ?? 1;
        $category->Allow_Ads = $request->Allow_Ads ?? 1;
        if ($request->hasFile('imagepath')) {
            $imageName = time() . '.' . $request->imagepath->extension();
            $request->imagepath->move(public_path('asset/img/categories'), $imageName);
            $category->imagepath = 'asset/img/categories/' . $imageName;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('success', 'Category updated successfully!');
    }



    public function destroy($id)
    {
        $category = Category::find($id);

        if($category->products()->count()>0){
            return redirect()->route('admin.categories')->with('error','Cannot delete category with associated products.');
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
    }



    public function getCatId(Request $request)
    {
        $category = Product::where("id", $request->id)->get();

        if (!$category) {
            return $this->ErrorMessage("error");
        }

        return response()->json(['msg' => 'get Catid', 'data' => $category]);
    }

    ////admin
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'ASC');   // default ASC
        $categories = Category::orderBy('id', $sort)->get();

        return view('admin.categories.index', compact('categories', 'sort'));
    }
}
