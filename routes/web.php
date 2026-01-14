<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FirstController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProducttController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Models\product;
use Illuminate\Http\Request;


use App\Http\Controllers\AuthController;

Auth::routes();
// Admin Dashboard
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::get('admin/productstable',[AdminController::class,'productsTable'])->name('admin.products.table')->name('productstable');


// Products
Route::get('/admin/products', [ProducttController::class, 'index'])->name('admin.products');
Route::get('/admin/products/create', [ProducttController::class, 'create'])->name('admin.products.create');
Route::post('/admin/products/store', [ProducttController::class, 'store'])->name('admin.products.store');

Route::get('/admin/users', [ProducttController::class, 'index'])->name('admin.users');
// Categories
Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories');
Route::get('/admin/categories/create', [CategoryController::class, 'showCreateForm'])->name('admin.categories.create');
Route::post('/admin/categories/store', [CategoryController::class, 'store'])->name('admin.categories.store');
Route::get('/admin/categories/{id}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit'); //show edit form
Route::put('/admin/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update'); //handle update
Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy'); //


/////////////////////////////////

Route::get('/', [FirstController::class, 'Master'])->name('welcome');
Route::get('/home', [FirstController::class, 'home'])->name('home');
Route::get('/cook', [FirstController::class, 'CookWhatYouHave'])->name('cook');
Route::post('/cook/search', [FirstController::class, 'SearchMeals'])->name('cook.search');

// عرض الكاتيجوريز
// صفحة عرض المنتجات + فلترة بالكاتيجوري
Route::get('/product/{catid?}', [FirstController::class, 'GetShopProducts'])->name('product.id');
Route::get('/category', [FirstController::class, 'GetCatProducts'])->name('category');
Route::get('/counter', [FirstController::class, 'counter'])->name('livewire.counter');

// عرض المنتجات (فلترة بالكاتيجوري)
Route::get('/shop/{catid?}', [FirstController::class, 'GetShopProducts'])->name('shop');

// عرض تفاصيل منتج
Route::get('/shopdetail/{id}', [FirstController::class, 'GetShopDetails'])->name('shop.detail');


Route::delete('/removeproduct/{productid}', [ProducttController::class, 'RemoveProduct'])->name('product.remove');

// عرض الفورم (edit page)
Route::get('/editproduct/{productid}', [ProducttController::class, 'EditProduct'])->name('product.edit')->middleware('auth');

// تحديث المنتج (update action)
Route::put('/updateproduct/{productid}', [ProducttController::class, 'UpdateProduct'])->name('product.update');

Route::get('/review', [FirstController::class, 'Reviews'])->name('review');
Route::post('/storereview', [ProducttController::class, 'StoreReview'])->name('reviews.store');
Route::get('/search', [ProducttController::class, 'Search'])->name('search');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('users/{id}', function ($d) {});
Route::middleware('auth')->group(function () {
    Route::get('/addproduct', [ProducttController::class, 'AddProduct'])->name('add.product');
    Route::post('/storeproduct', [ProducttController::class, 'StoreProduct'])->name('store.product');
});

Route::get('/productstable',[ProducttController::class,'productsTable'])->name('products.table')->name('productstable');

Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index')->middleware('auth');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');


// Route::middleware(['verify.token'])->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
// });

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');


