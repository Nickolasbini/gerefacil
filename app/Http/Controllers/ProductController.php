<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Document;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
        $weight         = $this->getParameter('weight');
        $length         = $this->getParameter('length');
        $width          = $this->getParameter('width');
        $height         = $this->getParameter('height');
        if(!$name || !$categoryId || !$price || !$quantity || !$productDetails || !$images || !$weight || !$length || !$width || !$height){
            Functions::translateAndSetToSession('required data missing', 'failure');
            return redirect()->back();
        }
        if($height < 10){
            Functions::translateAndSetToSession('height must be higher than 9', 'failure');
            return redirect()->back();
        }
        $productObj = new Product();
        if($id){
            $product = $productObj->find($id);
            if(!$product){
                Functions::translateAndSetToSession('invalid', 'failure');
                redirect()->back();
            }
            $productObj = $product;
            if(!$productObj->document){
                Functions::translateAndSetToSession('at least a photo is required', 'failure');
                return redirect()->back();
            }
        }else{
            if(count($images) == 0){
                Functions::translateAndSetToSession('at least a photo is required', 'failure');
                return redirect()->back();
            }
        }
        
        $categoryObj = new Category();
        $category = $categoryObj->find($categoryId);
        if(!$category){
            Functions::translateAndSetToSession('category is invalid', 'failure');
            return redirect()->back();
        }
        $documentId = ($id ? $productObj->document : null);
        if($images && count($images) > 0){
            $cropData = [
                'imgWidth'  => 200,
                'imgHeight' => 200,
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
            return redirect()->back();
        }
        $result = Product::updateOrCreate(
            ['id' => $id],
            [
                'name'               => $name,
                'category_id'        => $categoryId,
                'productDetails'     => $productDetails, 
                'price'              => Functions::parsePriceToDB($price), 
                'quantity'           => $quantity,
                'weightInKM'         => $weight,
                'lengthInCentimeter' => $length,
                'widthInCentimeter'  => $width,
                'user_id'            => $this->getLoggedUserId(), 
                'document'           => $documentId,
                'heightInCentimeter' => $height
            ]
        );
        if(!$result){
            Functions::translateAndSetToSession('an error occured, try again later', 'failure');
            return redirect()->back();
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
        $typeOfShipment = $this->getParameter('sipmentType');
        $product = Product::find($productId);
        if(!$product){
            Functions::translateAndSetToSession('invalid product', 'failure');
            return redirect()->back();
        }
        $productOwnerUser = User::find($product->user_id);
        $productOwnerCep = $productOwnerUser->cep;
        $cepData = [
            'value'       => null,
            'deliverTime' => null
        ];
        if(Auth::user()){
            $productLikeObj = \App\Models\ProductLikes::where('product_id', $product->id)->where('user_id', Auth::user()->id)->get();
            if(count($productLikeObj) > 0){
                $product->iLiked = true;
            }else{
                $product->iLiked = false;
            }
            $favoriteObj = \App\Models\Favorite::where('product_id', $product->id)->where('user_id', Auth::user()->id)->get();
            if(count($favoriteObj) > 0){
                $product->myFavorite = true;
            }else{
                $product->myFavorite = false;
            }
        }
        $cepData = [
            'value'       => null,
            'deliverTime' => null
        ];
        if(Auth::user() && Auth::user()->cep){
            $shipment = new \App\Models\Shipment(
                Auth::user()->cep, 
                $productOwnerCep, 
                $product->weightInKM,
                $product->lengthInCentimeter, 
                $product->widthInCentimeter, 
                $product->heightInCentimeter, 
                $product->price,
                $typeOfShipment
            );
            $cepData = [
                'value'       => $shipment->getValor(),
                'deliverTime' => $shipment->getPrazoEntrega()
            ];
        }else{
            $shipment = new Shipment();
        }
        
        return view('dashboard/product_views/product_detail')->with([
            'product'         => $product,
            'admin'           => (Auth::user() && Auth::user()->administrator ? true : false),
            'productOwnerCep' => $productOwnerCep,
            'cepData'         => $cepData,
            'shipmentTypes'   => $shipment->getShipmentTypes(),
            'selectedShipmentType' => $shipment->getShipmentTypes()['Sedex'],
            'orderId'         => (Auth::user() ? ($orderObj = new Order())->getIdOfActiveOrder() : null) 
        ]);
    }

    // calculates shipment accordingly to sent data
    public function calculateShipment()
    {
        $productId    = $this->getParameter('productId');
        $cep          = $this->getParameter('cep');
        $shipmentType = $this->getParameter('shipmentType');
        if(!$cep || !$shipmentType){
            return json_encode([
                'success' => false,
                'message' => 'data missing'
            ]);
        }
        if(!$productId){
            return json_encode([
                'success' => false,
                'message' => 'no product selected'
            ]);
        }
        $productObj = Product::find($productId);
        if(!$productObj){
            return json_encode([
                'success' => false,
                'message' => 'invalid product'
            ]);
        }
        $productOwnerUser = User::find($productObj->user_id);
        $productOwnerCep = $productOwnerUser->cep;
        $cepData = [
            'value'       => null,
            'deliverTime' => null
        ];
        $specificationsData = $productObj->calculateSpecificationsCubicSizeOfProduct($productObj);
        $shipment = new \App\Models\Shipment(
            $cep, 
            $productOwnerCep, 
            $specificationsData['weight'],
            $specificationsData['length'], 
            $specificationsData['width'], 
            $specificationsData['height'], 
            $productObj->price,
            $shipmentType
        );
        $cepData = [
            'value'       => Functions::formatMoney($shipment->getValor()),
            'deliverTime' => $shipment->getPrazoEntrega()
        ];
        return json_encode([
            'success' => true,
            'message' => 'data gathered',
            'content' => $cepData
        ]);
    }

    // favorites a product.
    // obs: if you already favorited this product it will your favoritation else will favorite it
    public function favoritePorduct()
    {
        $productId = $this->getParameter('productId');
        if(!$productId){
            return json_encode([
                'success' => false,
                'message' => 'no product selected'
            ]);
        }
        $productObj = Product::find($productId);
        if(!$productObj){
            return json_encode([
                'success' => false,
                'message' => 'invalid product'
            ]);
        }
        if(Auth::user()->id == $productObj->user_id){
            return json_encode([
                'success' => false,
                'message' => "you can't favorite your own product"
            ]);
        }
        $favoriteObj = Favorite::where('user_id', $this->getLoggedUserId())->where('product_id', $productId)->get();
        if(count($favoriteObj) > 0){
            $result  = $favoriteObj[0]->delete();
            $message = ucfirst(translate('product removed from your favorites'));
            $added = false;
        }else{
            $result = Favorite::create([
                'product_id' => $productObj->id,
                'user_id'    => $this->getLoggedUserId()
            ]);
            $message = ucfirst(translate('product added to your favorites'));
            $added = true;
            $result = (is_object($result) ? true : false);
        }
        return json_encode([
            'success' => $result,
            'message' => ($result ? $message : ucfirst(translate('an error occured, try again'))),
            'added'   => $added
        ]);
    }
}