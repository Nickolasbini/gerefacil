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
            $categoryObj = $category;
        }else{
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('category is required'))
            ]);
        }
        $result = Product::updateOrCreate(
            ['id' => $id],
            ['name' => $name, 'category_id' => $categoryId, 'productDetails' => $productDetails, 'price' => $price, 'quantity' => $quantity, 'user_id' => $this->session->get('authUser-id')]
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
}