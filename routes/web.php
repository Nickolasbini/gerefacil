<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;

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

Route::middleware(['master'])->get('/', function () {
    return view('welcome');
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
    // products routes
    
    Route::post('product/remove', [ProductController::class, 'save']);

    // products routes
    Route::get('sale/save', [SalesController::class, 'save']);

    // products routes
    Route::get('message/save', [MessageController::class, 'save']);

    // products routes
    Route::get('report/save', [ReportController::class, 'save']);
});

Route::get('product/save', [ProductController::class, 'save']);