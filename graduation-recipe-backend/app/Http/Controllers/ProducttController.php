<?php

namespace App\Http\Controllers;

use Yajra\DataTables\DataTables;


use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProducttController extends Controller
{
    # عرض فورم إضافة منتج
    public function AddProduct()
    {
        $user = Auth::user();
        $allcategories = Category::all();
        return view('layouts.products.addproduct', compact('allcategories'));
    }

    public function StoreProduct(Request $request)
    {
        $request->validate([
            'name_en' => 'required|max:50|unique:products',
            'name_ar' => 'required|max:50',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'imagepath' => 'required|image|mimes:png,jpg,gif,jpeg|max:2048',
            'description' => 'required',
            'cat_id' => 'required|exists:categories,id'
        ]);

        $newproduct = new Product();
        $newproduct->name_en = $request->name_en;
        $newproduct->name_ar = $request->name_ar;
        $newproduct->price = $request->price;
        $newproduct->quantity = $request->quantity;
        $newproduct->description = $request->description;
        $newproduct->cat_id = $request->cat_id;

        // correct image upload
        if ($request->hasFile('imagepath')) {

            $imageName = time() . '.' . $request->imagepath->extension();
            $request->imagepath->move(public_path('asset/img'), $imageName);

            $newproduct->imagepath = 'asset/img/' . $imageName;
        }

        $newproduct->save();

        return redirect('/shop')->with('success', 'Product added successfully!');
    }



    ////////admin functions
    public function showCreateForm()
    {
        $products = Product::all();
        return view('admin.products.add', compact('products'));
    }
    public function create()
    {
        $user = Auth::user();
        $allcategories = Category::all();
        return view('admin.products.add', compact('allcategories'));
    }





    # حذف منتج
    public function RemoveProduct($productid)
    {
        $product = Product::find($productid);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        $product->delete();
        return redirect()->route('shop')->with('success', 'Product deleted successfully');
    }

    # عرض صفحة تعديل
    public function EditProduct($productid)
    {
        $product = Product::find($productid);
        if (!$product) {
            return redirect('/shop')->with('error', 'Product not found');
        }

        $categories = Category::all();
        return view('layouts.products.editproduct', compact('product', 'categories'));
    }

    # تحديث المنتج
    public function UpdateProduct(Request $request, $productid)
    {
        $product = Product::find($productid);

        if (!$product) {
            return redirect('/shop')->with('error', 'Product not found');
        }
        $request->validate([
            'name_en' => 'required|max:50',
            'name_ar' => 'required|max:50',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $product->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'cat_id' => $request->cat_id,
        ]);


        if ($request->hasFile('imagepath')) {
            $imageName = time() . '.' . $request->imagepath->extension();
            $request->imagepath->move(public_path('asset/img'), $imageName);
            $product->imagepath = 'asset/img/' . $imageName;
            $product->save();
        }

        return redirect('/shop')->with('success', 'Product updated successfully!');
    }

    # تخزين مراجعة
    public function StoreReview(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'phone'   => 'required',
            'email'   => 'required|email',
            'subject' => 'required',
            'message' => 'required',
            'imagepath' => 'nullable|string',
        ]);

        Review::create($request->all());

        return redirect('/shop')->with('success', 'Review submitted!');
    }

    public function search(Request $request)
    {
        $key = $request->input('searchkey');
        $products = Product::where('name_en', 'LIKE', "%{$key}%")
            ->orWhere('name_ar', 'LIKE', "%{$key}%")
            ->paginate(4);

        return view('layouts.search', compact('products'));
    }


    public function productsTable()
    {
        $products = Product::all(); // ✅ send all rows
        return view('layouts.products.productstable', ['products' => $products]);
    }

    ////admin
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', [
            'products' => $products
        ]);
    }
}
