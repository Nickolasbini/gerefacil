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
    Route::get('/', function () {
        return view('home');
    });
    Route::post('user/validatecpf', [UserController::class, 'validateCPF']);
    Route::post('user/getcepdata', [UserController::class, 'getCEPData']);
    Route::post('user/checkserial', [UserController::class, 'checkSerial']);
});

// sadly I can not create a group for some reason (maybe cause of jetstream)
Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard/product', function () {
    return view('dashboard/product_home');
})->name('dashboard/product');

Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard/sale', function () {
    return view('dashboard/sale_home');
})->name('dashboard/sale');

Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard/message', function () {
    return view('dashboard/message_home');
})->name('dashboard/message');

Route::middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->get('/dashboard/report', function () {
    return view('dashboard/report_home');
})->name('dashboard/report');

Route::prefix('dashboard')->middleware(['master', 'auth:sanctum', 'verified', 'authenticatedUserActions'])->group(function () {
    Route::get('getprofilephoto', [UserController::class, 'getProfilePhoto']);

    // products routes
    Route::get('product/save/{productId?}', [ProductController::class, 'create']);
    Route::post('product/save', [ProductController::class, 'save']);

    // products routes
    Route::get('sale/save', [SaleController::class, 'save']);

    // products routes
    Route::get('message/save', [MessageController::class, 'save']);

    // products routes
    Route::get('report/save', [ReportController::class, 'save']);
});

Route::get('category/list', [CategoryController::class, 'list']);