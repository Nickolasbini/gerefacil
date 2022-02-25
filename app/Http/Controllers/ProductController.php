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
        $limit       = $this->getParameter('limit', 10);
        $filter      = $this->getParameter('q');
        $page        = $this->getParameter('page', 1);

        //$a = Category::find(1);
        //dd($a->getUser()->name);

        $elements = []; 
        $loggedUserId = $this->getLoggedUserId();
        if($filter){
           $total = Product::where(function ($query) use ($loggedUserId) {
                $query->where('user_id', $loggedUserId);
            })
            ->where(function ($query) use ($filter) {
                $query->where('name', 'like', '%'.$filter.'%');
            })->count();
        }else{
            $total = Product::where('user_id', $this->getLoggedUserId())->orWhere('user_id', null)->count();
        }
        if($total < 0){
            return json_encode([
                'success' => false,
                'content' => $elements
            ]);
        }
        if($filter){
            $products = Product::where(function ($query) use ($loggedUserId) {
                $query->where('user_id', $loggedUserId);
            })
            ->where(function ($query) use ($filter) {
                $query->where('name', 'like', '%'.$filter.'%');
            })->paginate($limit);
        }else{
            $products = Product::where('user_id', $this->getLoggedUserId())->paginate($limit);
        }
        $allCategories = $this->getIndexedArray('id', 'name', (new Category())->getMyCategories());
        $elements = [];
        if($products->count() > 0){
            foreach($products->items() as $product){
                // it's faster to do it by hand
                $element = [
                    'id'             => $product->id,
                    'name'           => $product->name,
                    'price'          => $product->price,
                    'quantity'       => $product->quantity,
                    'category'       => $allCategories[$product->category_id],
                    'productDetails' => $product->productDetails,
                    'photos'         => ($product->photosReferences ? '<a>see photo</a>' : 'plus icon'),
                    'created_at'     => Functions::formatDate($product->created_at),
                    'updated_at'     => Functions::formatDate($product->updated_at, 'd-m-Y h:i'),
                ];
                $elements[] = $element;
            }
        }
        $additionalParameters = [
            'editUrl'      => 'dashboard/product/save',
            'translations' => [
                'name'           => ucfirst(translate('name')),
                'price'          => ucfirst(translate('price')),
                'quantity'       => ucfirst(translate('quantity')),
                'category'       => ucfirst(translate('category')),
                'productDetails' => ucfirst(translate('productDetails')),
                'photos'         => ucfirst(translate('photos')),
                'created_at'     => ucfirst(translate('created at')),
                'updated_at'     => ucfirst(translate('updated at'))
            ]
        ];
        $toHide = ($filter ? ['id', 'filter'] : ['id']);
        $tableWk = new TableGenerator();
        $htmlOfTable = $tableWk->generateHTMLTable($elements, $toHide, $additionalParameters);
        if($filter){
            return json_encode([
                'success' => count($products) > 0 ? true : false,
                'content' => $htmlOfTable
            ]);
        }
        return view('dashboard/product_views/product_home')->with([
            'content'    => $htmlOfTable,
            'page'       => $products,
            'pageNumber' => $page
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
        // get also all the categories of this user and the default ones and up it to view, there, select the one that belongs to this user if this is an update
        return view('dashboard/product_views/create_product')->with(['product' => $productObj, 'category' => $allCategories]);
    }
}