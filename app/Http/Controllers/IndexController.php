<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class IndexController extends Controller 
{
    // homepage related data to be sent to view
    public function homePage($page = 1)
    {
        dd(Functions::sequenciaCrescente());
        exit(Functions::fetchRandomArrayAndUnrepeatedNumbers());
        exit(Functions::primos($this->getParameter('number1'), $this->getParameter('number2')));
        exit(Functions::SeculoAno($this->getParameter('year')));
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
        return view('home')->with([
            'products'   => $products,
            'categories' => $categories,
            'search'     => $productName,
            'page'       => $page,
            'filter'     => $filter,
            'filteringOptions' => $filteringOptions,
            'selectedCategory' => ($filter == 'category' ? $productName : null)
        ]);
    }

    // changes the system language session variable
    public function changeLanguage()
    {
        $seletedLanguage = $this->getParameter('seletedLanguage', env('USER_LANGUAGE'));
        $avaliableLanguages = ['en', 'pt', 'es'];
        if(!in_array($seletedLanguage, $avaliableLanguages)){
            return redirect('/');
        }
        session()->put('userLanguage', $seletedLanguage);
        return redirect('/');
    }
}