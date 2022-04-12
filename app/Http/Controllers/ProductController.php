<?php

namespace App\Http\Controllers;

use App\Helpers\TableGenerator;
use App\Helpers\Functions;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Document;

class ProductController extends Controller 
{
    /**
     * Create or Update
     * @param  <int> id for update
     * 
     * @return 
    */
    public function save($productId = null)
    {
        $id             = $productId;
        $name           = $this->getParameter('name');
        $categoryId     = $this->getParameter('category');
        $productDetails = $this->getParameter('productDetails');
        $price          = $this->getParameter('price');
        $price          = str_replace(',', '.', $price);
        $quantity       = $this->getParameter('quantity');
        $images         = $this->getParameter('files');
        if(!$name || !$categoryId || !$price || !$quantity || !$productDetails || !$images){
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
        $documentId = null;
        if($images && count($images) > 0){
            $cropData = [
                'imgWidth'  => 160,
                'imgHeight' => 300,
                'imgX'      => 0,
                'imgY'      => 0,
                'resize'    => true
            ];
            $documentObj = new Document();
            // later change this in order to loop throught many photos
            $images = $this->request->file('files')[0];
            $document = $documentObj->saveAnImageWithCrop($images, $cropData);
            if(!is_object($document)){
                Functions::translateAndSetToSession(ucfirst(translate('invalid image(s)')), 'failure', false);
                if($id){
                    return Functions::redirectToURI('product/save/'.$id);
                }else{
                    return Functions::redirectToURI('product/save');
                }
            }
            $documentId = $document->id;
        }
        // verify if user_id is the same as mine or if it's from the superAdmin User
        $userObj = $category->getUser();
        if($userObj->id != $this->getLoggedUserId() && !$userObj->master_admin){
            Functions::translateAndSetToSession('category can not be user', 'failure');
            return Functions::redirectToURI();
        }
        $result = Product::updateOrCreate(
            ['id' => $id],
            ['name' => $name, 'category_id' => $categoryId, 'productDetails' => $productDetails, 'price' => Functions::parsePriceToDB($price), 'quantity' => $quantity, 'user_id' => $this->getLoggedUserId(), 'document' => $documentId]
        );
        if(!$result){
            Functions::translateAndSetToSession('an error occured, try again later', 'failure');
            return Functions::redirectToURI();
        }
        $message = ($id ? ucfirst(translate('product updated')) : ucfirst(translate('product created')));
        Functions::translateAndSetToSession($message, 'success');
        return Functions::redirectToURI('dashboard/product/save/' . $result->id);
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
        $products = Product::where('id', '>', 0)->where('user_id', $this->getLoggedUserId())->orderBy('created_at', 'desc')->paginate($limit);
        return view('dashboard/product_home')->with([
            'products' => $products
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
        $documentObj = new Document();
        $docId = $product->document;
        $result = $product->delete();
        if(!$result){
            return json_encode([
                'success' => false,
                'message' => Functions::translateAndSetToSession('an error occured')
            ]);
        }
        $documentObj->removeObjectAndFile($docId);
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

    // adds or removes 1 like
    public function handleLike()
    {
        $productId = $this->getParameter('productId');
        $productLikeObj = new \App\Models\ProductLikes();
        $result = $productLikeObj->addLike($productId);
        if($result != 'added' && $result != 'removed'){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('no changes'))
            ]);
        }
        return json_encode([
            'success' => true,
            'message' => ucfirst(translate('updated')),
            'added'   => ($result == 'added' ? true : false)
        ]);
    }

    // gather product detail and calls its view
    public function productDetail($productId = null)
    {
        $product = Product::find($productId);
        if(!$product){
            Functions::translateAndSetToSession('invalid product', 'failure');
            return redirect()->back();
        }
        return view('dashboard/product_views/product_detail')->with([
            'product' => $product
        ]);
    }
}