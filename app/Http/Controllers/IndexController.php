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
        // $frete = new \App\Models\Shipment(
        //      83702185, 
        //      83820429, 
        //      5, 
        //      10, 
        //      10, 
        //      10, 
        //      55
        // );
        
        // echo $frete->getValor();//Obtem o valor do frete
        // echo '<br>';
        // echo $frete->getPrazoEntrega();//Obtem o prazo de entrega em dias
	    // die;

        $productName = $this->getParameter('search');
        $limit       = $this->getParameter('limit', 10);
        $filter      = $this->getParameter('filter');
        switch($filter){
            case 'cheaper':
                if($productName){
                    $products = Product::where('name',' like', '%'.$productName.'%')->orderBy('price', 'asc')->paginate($limit);
                }else{
                    $products = Product::where('id', '>', '0')->orderBy('price', 'asc')->paginate($limit);
                }
            break;
            case 'expensive':
                if($productName){
                    $products = Product::where('name',' like', '%'.$productName.'%')->orderBy('price', 'desc')->paginate($limit);
                }else{
                    $products = Product::where('id', '>', '0')->orderBy('price', 'desc')->paginate($limit);
                }
            break;
            case 'category':
                if($productName){
                    // in this case category id
                    $products = Product::where('category_id', $productName)->orderBy('price')->paginate($limit);
                }
            break;
            default:
                if($productName){
                    $products = Product::where('name', 'like', '%'.$productName.'%')->paginate($limit);
                }else{
                    $products = Product::where('id', '>', '0')->paginate($limit);
                }
            break;
        }
        $categoryObj = new Category();
        $categories = $categoryObj->getAndParse('idAndName');
        $filteringOptions = [
            'cheaper'   => ucfirst(translate('cheaper')),
            'expensive' => ucfirst(translate('expensive'))
        ];
        if(Auth::user()){
            foreach($products as $product){
                $productLikeObj = \App\Models\ProductLikes::where('product_id', $product->id)->where('user_id', Auth::user()->id)->get();
                if(count($productLikeObj) > 0){
                    $product->iLiked = true;
                }else{
                    $product->iLiked = false;
                }
            }
        }
        return view('home')->with([
            'products'   => $products,
            'categories' => $categories,
            'search'     => $productName,
            'page'       => $page,
            'filter'     => $filter,
            'filteringOptions' => $filteringOptions
        ]);
    }

}