<?php

namespace App\Http\Controllers;

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
        $categoryId     = $this->getParameter('categoryId');
        $productDetails = $this->getParameter('productDetails');
        $price          = $this->getParameter('price');
        $price          = str_replace(',', '.', $price);
        $quantity       = $this->getParameter('quantity');
        $productObj = new Product();
        if($id){
            $product = $productObj->find($id);
            if(!$product){
                return json_encode([
                    'success' => false,
                    'message' => ucfirst(translate('invalid'))
                ]);
            }
            $productObj = $product;
        }
        if($categoryId){
            $categoryObj = new Category();
            $category = $categoryObj->find($categoryId);
            if(!$category){
                return json_encode([
                    'success' => false,
                    'message' => ucfirst(translate('category is invalid'))
                ]);
            }
            // verify if user_id is the same as mine or if it's from the superAdmin User
            $userObj = $category->getUser();
            if($userObj->id != $this->getLoggedUserId() && !$userObj->isSuperAdmin()){
                return json_encode([
                    'success' => false,
                    'message' => ucfirst(translate('category can not be user'))
                ]);
            }
            $categoryObj = $category;
        }else{
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('category is required'))
            ]);
        }
        $result = Product::updateOrCreate(
            ['id' => $id],
            ['name' => $name, 'category_id' => $categoryId, 'productDetails' => $productDetails, 'price' => $price, 'quantity' => $quantity, 'user_id' => $this->getLoggedUserId()]
        );
        if(!$result){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('an error occured, try again later'))
            ]);
        }
        $message = ($id ? ucfirst(translate('product updated')) : ucfirst(translate('product created')));
        return json_encode([
            'success' => true,
            'message' => $message,
            'id'      => $productObj->id
        ]);
    }
      
    /**
     * List all 
     * @param  page <int> 
     * 
     * @return view
    */
    public function list()
    {
        
    }

    /**
     * Remove 
     * @param  id   to remove
     * 
     * @return
    */
    public function remove()
    {
        
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
        $this->session->put('authUser-id', 1);
        $category = new Category();
        $categoryNamesAndIds = $category->getAll(true);
        // get also all the categories of this user and the default ones and up it to view, there, select the one that belongs to this user if this is an update
        return view('dashboard/product_views/create_product')->with(['product' => $productObj, 'category' => $categoryNamesAndIds]);
    }
}