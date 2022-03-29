<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller 
{
    // homepage related data to be sent to view
    public function homePage($page = 1)
    {
        $productName = $this->getParameter('search');
        $limit       = $this->getParameter('limit', 10);
        $filter      = $this->getParameter('filter');
        switch($filter){
            case 'cheaper':
                if($productName){
                    $products = Product::where('name',' like', '%'.$productName.'%')->orderBy('price', 'desc')->paginate($limit);
                }else{
                    $products = Product::where('id', '>', '0')->orderBy('price', 'desc')->paginate($limit);
                }
            break;
            case 'expensive':
                if($productName){
                    $products = Product::where('name',' like', '%'.$productName.'%')->orderBy('price')->paginate($limit);
                }else{
                    $products = Product::where('id', '>', '0')->orderBy('price')->paginate($limit);
                }
            break;
            default:
                if($productName){
                    $products = Product::where('name',' like', '%'.$productName.'%')->paginate($limit);
                }else{
                    $products = Product::where('id', '>', '0')->paginate($limit);
                }
            break;
        }
        $categoryObj = new Category();
        $categories = $categoryObj->getAndParse('idAndName');
        return view('home')->with([
            'products'   => $products,
            'categories' => $categories
        ]);
    }

}