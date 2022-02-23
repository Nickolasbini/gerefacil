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
            if($userObj->id != $this->getLoggedUserId() && !$userObj->master_admin){
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
        $page   = $this->getParameter('page', 1);
        $limit  = $this->getParameter('limit', 10);
        $filter = $this->getParameter('filter');

        $this->session->put('authUser-id', 5);

        $elements = [];
        $total = Product::where('user_id', $this->getLoggedUserId())->orWhere('user_id', null)->count();
        if($total < 0){
            return json_encode([
                'success' => false,
                'content' => $elements
            ]);
        }
        $products = Product::where('id', '>', '0')->paginate($limit);
        $allCategories = $this->getIndexedArray('id', 'name', (new Category())->getMyCategories());

        $elements = [];
        foreach($products->items() as $product){
            // it's faster to do it by hand
            $element = [
                'id'             => $product->id,
                'name'           => $product->name,
                'price'          => $product->price,
                'quantity'       => $product->price,
                'category'       => $allCategories[$product->category_id],
                'productDetails' => $product->price,
                'photos'         => ($product->photosReferences ? '<a>see photo</a>' : 'plus icon'),
                'created_at'     => Functions::formatDate($product->created_at),
                'updated_at'     => Functions::formatDate($product->updated_at),
            ];
            $elements[] = $element;
        }
        $translations = [
            'name'           => ucfirst(translate('name')),
            'price'          => ucfirst(translate('price')),
            'quantity'       => ucfirst(translate('quantity')),
            'category'       => ucfirst(translate('category')),
            'productDetails' => ucfirst(translate('productDetails')),
            'photos'         => ucfirst(translate('photos')),
            'created_at'     => ucfirst(translate('created at')),
            'updated_at'     => ucfirst(translate('updated at'))
        ];
        $tableWk = new TableGenerator();
        $htmlOfTable = $tableWk->generateHTMLTable($elements, ['id', 'category_id', 'photosReferences', 'user_id'], ['removeUrl' => 'dashboard/product/remove', 'translations' => $translations]);
        return view('dashboard/product_home')->with([
            'content' => $htmlOfTable,
            'page'    => $products
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
        
    }
}