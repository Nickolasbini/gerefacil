<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// normal routes
Route::middleware(['master'])->group(function(){ 
    Route::get('/', 'IndexController@homePage')->name('/');

    Route::post('productorder/additem', 'ProductOrderController@addItem')->name('productorder.additem');
    Route::post('productorder/listcart', 'ProductOrderController@listCart')->name('productorder.listcart');
    Route::post('productorder/updateitemquantity', 'ProductOrderController@updateItemQuantity')->name('productorder.updateitemquantity');
    Route::post('productorder/removeproductorder', 'ProductOrderController@removeProductOrder')->name('productorder.removeproductorder');

    Route::post('user/validatecpf', 'UserController@validateCPF')->name('validate.cpf');
    Route::post('user/getcepdata', 'UserController@getCEPData')->name('get.cep.data');
    Route::post('user/checkserial', 'UserController@checkSerial')->name('check.serial');

    // public routes
    Route::get('product/detail/{productId?}', 'ProductController@productDetail')->name('product.detail');
    Route::post('calculateshipment', 'ProductController@calculateShipment')->name('calculate.shipment');
    Route::post('changelanguage', 'IndexController@changeLanguage')->name('change.language');

    // clean session viewMessage
    Route::post('cleansessionmessage', 'UserController@cleanViewMessage')->name('cleansessionmessage');
});

Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard/product', function () {
    $products = \App\Models\Product::where('id', '>', 0)->where('user_id', session()->get('authUser-id'))->orderBy('created_at', 'desc')->paginate(10);
    return view('dashboard/product_views/product_home')->with([
        'products' => $products
    ]);
})->name('dashboard.product');

Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard/sale', function () {
    return view('dashboard/sale_views/sale_home');
})->name('dashboard.sale');

Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard/message', function () {
    return view('dashboard/message_views/message_home');
})->name('dashboard.message');

Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard/report', function () {
    return view('dashboard/report_views/report_home');
})->name('dashboard.report');

Route::prefix('dashboard')->middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->group(function () {
    Route::get('/', 'DashboardController@dashboard')->name('dashboard');
    Route::get('home', 'DashboardController@dashboard')->name('dashboard/home');

    Route::get('getprofilephoto', [UserController::class, 'getProfilePhoto']);

    // products routes
    Route::get('product/list', 'ProductController@list')->name('product.list');
    Route::get('product/save/{productId?}', 'ProductController@create')->name('product.create');
    
    Route::post('product/save/{productId?}', 'ProductController@save')->name('product.save');
    Route::post('product/remove', 'ProductController@remove')->name('product.remove');

    Route::post('product/handlelikes', 'ProductController@handleLike')->name('product.handlelikes');
    Route::post('product/favoriteproduct', 'ProductController@favoritePorduct')->name('product.favoriteproduct');

    // sale routes
    Route::get('sale/save', [SaleController::class, 'save']);

    // message routes
    Route::get('message/save', [MessageController::class, 'save']);

    // report routes
    Route::get('report/save', [ReportController::class, 'save']);

    // favorite routes
    Route::get('favorite/list', 'FavoriteController@list')->name('favorite.list');
    Route::post('favorite/remove', 'FavoriteController@remove')->name('favorite.remove');

    // order routes
    Route::post('order/additem', 'OrderController@addItem')->name('order.additem');
});

// authenticated routes without prefix 
Route::middleware(['master', 'authenticatedUserActions'])->group(function () {
    // product order routes
    Route::get('cart', 'ProductOrderController@myCart')->name('cart');
});

Route::post('calculateordershipment', 'OrderController@calculateOrderShipmentPriceAndDelivery')->name('calculateordershipment');
Route::post('handleproductorderquantity', 'ProductOrderController@handleProductOrderQuantity')->name('handleproductorderquantity');
Route::post('orderdetail', 'OrderController@orderDetail')->name('orderdetail');