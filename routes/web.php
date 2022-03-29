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
    Route::get('/{page?}', 'IndexController@homePage')->name('/');

    Route::post('user/validatecpf', 'UserController@validateCPF')->name('validate.cpf');
    Route::post('user/getcepdata', 'UserController@getCEPData')->name('get.cep.data');
    Route::post('user/checkserial', 'UserController@checkSerial')->name('check.serial');
});

// dashboard routes
Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard/home', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard/product', function () {
    return view('dashboard/product_views/product_home');
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
    Route::get('getprofilephoto', [UserController::class, 'getProfilePhoto']);

    // products routes
    Route::get('product/list/{page?}', 'ProductController@list')->name('product.list');
    Route::get('product/save/{productId?}', 'ProductController@create')->name('product.create');
    
    Route::post('product/save/{productId?}', 'ProductController@save')->name('product.save');
    Route::post('product/remove', 'ProductController@remove')->name('product.remove');

    // products routes
    Route::get('sale/save', [SaleController::class, 'save']);

    // products routes
    Route::get('message/save', [MessageController::class, 'save']);

    // products routes
    Route::get('report/save', [ReportController::class, 'save']);

    // clean session viewMessage
    Route::post('cleansessionmessage', 'UserController@cleanViewMessage')->name('cleansessionmessage');
});
