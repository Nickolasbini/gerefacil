<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller 
{
    // dashboard homepage related data to be sent to view
    public function dashboard()
    {
        $page  = $this->getParameter('page');
        $limit = $this->getParameter('limit', 10);
        $products = Product::where('id', '>', 0)->where('user_id', $this->getLoggedUserId())->orderBy('created_at', 'desc')->paginate($limit);
        return view('dashboard')->with([
            'products' => $products
        ]);
    }
}