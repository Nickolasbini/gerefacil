<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProductOrderController extends Controller
{
    // add item to cart
    public function addItem()
    {
        $orderId   = $this->getParameter('orderId');
        $productId = $this->getParameter('productId');
        $quantity  = $this->getParameter('quantity');

        if(!$productId || !$quantity){
            return json_encode([
                'success' => false,
                'message' => 'missing parameters'
            ]);
        }
        $productObj = Product::find($productId);
        if(!$productObj){
            return json_encode([
                'success' => false,
                'message' => 'invalid product'
            ]);
        }
        if($orderId){
            $order = Order::find($orderId);
            if(!$order){
                return json_encode([
                    'success' => false,
                    'message' => 'invalid order'
                ]);
            }
            $orderObj = $order;
        }else{
            $orderObj = Order::create([
                'user_id' => $this->getLoggedUserId(), 'isPayed' => false, 'dateOfPayment' => Carbon::now()
            ]);
        }
        $productOrder = new ProductOrder();
        $response = $productOrder->addProduct($productObj, $orderObj, $this->request);
        if(!$response){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('an error occured, try again please'))
            ]);
        }
        return json_encode([
            'success' => true,
            'message' => ($quantity > 1 ? ucfirst(translate('items added')) : ucfirst(translate('item added')))
        ]);
    }

    // update quantity of a productOrder from cart
    public function updateItemQuantity()
    {
        $productOrderId = $this->getParameter('productOrderId');
        $quantity       = $this->getParameter('quantity');
        if(!$productOrderId || !$quantity){
            return json_encode([
                'success' => false,
                'message' => 'missing parameters'
            ]);
        }
        $productOrderObj = ProductOrder::find($productOrderId);
        if(!$productOrderObj){
            return json_encode([
                'success' => false,
                'message' => 'invalid product order'
            ]);
        }
        if($productOrderObj->quantity == $quantity){
            return json_encode([
                'success'  => true,
                'message'  => ucfirst(translate("quantity didn't change")),
            ]);
        }
        $response = $productOrderObj->updateQuantity($quantity);
        if(!$response){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('an error occured, try again please'))
            ]);
        }
        return json_encode([
            'success'  => true,
            'message'  => ucfirst(translate('quantity updated')),
            'newPrice' => $response
        ]);
    }

    // remove a product order from cart
    public function removeProductOrder()
    {
        $productOrderId = $this->getParameter('productOrderId');
        if(!$productOrderId){
            return json_encode([
                'success' => false,
                'message' => 'missing parameters'
            ]);
        }
        $productOrderObj = ProductOrder::find($productOrderId);
        if(!$productOrderObj){
            return json_encode([
                'success' => false,
                'message' => 'invalid product order'
            ]);
        }
        $quantity = $productOrderObj->quantity;
        $response = $productOrderObj->delete();
        if(!$response){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('an error occured, try again please'))
            ]);
        }
        return json_encode([
            'success'  => true,
            'message'  => ($quantity > 1 ? ucfirst(translate('items removed')) : ucfirst(translate('item removed')))
        ]);
    }

    // list my cart items
    public function listCart()
    {
        $page  = $this->getParameter('page', 1);
        $limit = $this->getParameter('limit', 20);
        $myOrder = Order::where('user_id', $this->getLoggedUserId())->get();
        if(count($myOrder) < 1){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('the cart is empty'))
            ]);
        }
        $productOrders = ProductOrder::where('order_id', $myOrder[0]->id)->paginate($limit);
        $response = [];
        foreach($productOrders as $productOrder){
            $productId    = null;
            $productImg   = null;
            $productName  = null;
            $productPrice = null;
            $productObj  = Product::find($productOrder->product_id);
            if($productObj){
                $productId    = $productObj->id;
                $productImg   = $productObj->getPhotoAsBase64();
                $productName  = $productObj->name;
                $productPrice = $productObj->price;
            }
            $response[] = [
                'id'           => $productOrder->id,
                'orderId'      => $myOrder[0]->id,
                'productId'    => $productId,
                'productPhoto' => $productImg,
                'productName'  => $productName,
                'unitaryPrice' => $productPrice,
                'parcialSum'   => $productOrder->totalSum,
                'quantity'     => $productOrder->quantity
            ];
        }
        return json_encode([
            'success' => true,
            'content' => $response,
            'total'   => count($response)
        ]);
    }

    // renders cart view and passes required data
    public function myCart()
    {
        $userObj = Auth::user();
        $orderObj = new Order();
        if(!$orderObj->haveAnyNonPayedOrder($userObj->id)){
            Functions::translateAndSetToSession('no open order', 'failure');
            return redirect()->back();
        }
        $myOrderObj    = $orderObj->getOpenOrder($userObj->id);
        $productOrders = $myOrderObj->getProductOrders();
        return view('authenticated/my_cart')->with([
            'order'        => $myOrderObj,
            'productOrder' => $productOrders
        ]);
    }
}

?>