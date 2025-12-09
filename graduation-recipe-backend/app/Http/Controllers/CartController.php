<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // نجيب كل منتجات الكارت الخاصة باليوزر الحالي
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        $grandTotal = $carts->sum(fn($item) => $item->product->price * $item->quantity);

        return view('layouts.cart', compact('carts', 'grandTotal'));
    }


    /////////////////////////////////

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // نشوف لو المنتج موجود بالفعل في الكارت
        $cart = Cart::where('user_id', Auth::id())
                    ->where('product_id', $id)
                    ->first();

        if ($cart) {
            $cart->quantity += 1;
            $cart->save();
        } else {
            Cart::create([
                'user_id'    => Auth::id(),
                'product_id' => $id,
                'quantity'   => 1,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }

    ////////////////////////////////

    public function update(Request $request, $id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->first();

        if ($cart && $request->quantity > 0) {
            $cart->quantity = $request->quantity;
            $cart->save();
        }

        return redirect()->route('cart.index')->with('success', 'Cart updated!');
    }


    ////////////////////////////////
    
    public function remove($id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->first();

        if ($cart) {
            $cart->delete();
        }

        return redirect()->route('cart.index')->with('success', 'Product removed!');
    }
}
