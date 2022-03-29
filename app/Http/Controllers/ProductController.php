<?php

namespace App\Http\Controllers;

use App\Helpers\TableGenerator;
use App\Helpers\Functions;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller 
{
    /**
     * Create or Update
     * @param  <int> id for update
     * 
     * @return 
    */
    public function save()
    {
        $id             = $this->getParameter('id');
        $name           = $this->getParameter('name');
        $categoryId     = $this->getParameter('category');
        $productDetails = $this->getParameter('productDetails');
        $price          = $this->getParameter('price');
        $price          = str_replace(',', '.', $price);
        $quantity       = $this->getParameter('quantity');
        $images         = $this->getParameter('images');
        if(!$name || !$categoryId || !$price || !$quantity){
            Functions::translateAndSetToSession('required data missing', 'failure');
            return Functions::redirectToURI();
        }
        $productObj = new Product();
        if($id){
            $product = $productObj->find($id);
            if(!$product){
                Functions::translateAndSetToSession('invalid', 'failure');
                Functions::redirectToURI();
            }
            $productObj = $product;
        }
        $categoryObj = new Category();
        $category = $categoryObj->find($categoryId);
        if(!$category){
            Functions::translateAndSetToSession('category is invalid', 'failure');
            return Functions::redirectToURI();
        }
        // verify if user_id is the same as mine or if it's from the superAdmin User
        $userObj = $category->getUser();
        if($userObj->id != $this->getLoggedUserId() && !$userObj->master_admin){
            Functions::translateAndSetToSession('category can not be user', 'failure');
            return Functions::redirectToURI();
        }
        $categoryObj = $category;
        $result = Product::updateOrCreate(
            ['id' => $id],
            ['name' => $name, 'category_id' => $categoryId, 'productDetails' => $productDetails, 'price' => $price, 'quantity' => $quantity, 'user_id' => $this->getLoggedUserId()]
        );
        if(!$result){
            Functions::translateAndSetToSession('an error occured, try again later', 'failure');
            return Functions::redirectToURI();
        }
        $message = ($id ? ucfirst(translate('product updated')) : ucfirst(translate('product created')));
        Functions::translateAndSetToSession($message, 'success');
        return Functions::redirectToURI($this->session->get('uri') . '/' . $result->id);
    }
      
    /**
     * List all 
     * @param  page <int> 
     * 
     * @return view
    */
    public function list()
    {
        $limit  = $this->getParameter('limit', 10);
        $filter = $this->getParameter('filter');
        $page   = $this->getParameter('page', 1);

        $products = Product::where('id', '>', 0)->paginate($limit);

        return view('dashboard/product_home')->with([
            
        ]);
    }

    /**
     * Remove 
     * @param  id   to remove
     * 
     * @return
    */
    public function remove()
    {
        $productId = $this->getParameter('productId');
        if(!$productId){
            return json_encode([
                'success' => false,
                'message' => Functions::translateAndSetToSession('invalid')
            ]);
        }
        // check if it isn't in any order, maybe remove there too or simply check there
        $product = Product::find($productId);
        if(!$product){
            return json_encode([
                'success' => false,
                'message' => Functions::translateAndSetToSession('invalid')
            ]);
        }
        $result = $product->delete();
        if(!$result){
            return json_encode([
                'success' => false,
                'message' => Functions::translateAndSetToSession('an error occured')
            ]);
        }
        return json_encode([
            'success' => true,
            'message' => Functions::translateAndSetToSession('removed with success')
        ]);
    }

    /**
     * Returns the create view 
     * @return view
    */
    public function create()
    {
        $productId = $this->getParameter('productId');
        $productObj = null;
        if($productId){
            $product = Product::find($productId);
            if($product){
                $productObj = $product;
            }
        }
        $allCategories = $this->getIndexedArray('id', 'name', (new Category())->getMyCategories());
        return view('dashboard/product_views/create_product')->with(['product' => $productObj, 'category' => $allCategories]);
    }
}