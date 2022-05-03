<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\Shipment;
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
        if(!Auth::user()){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('please log in first'))
            ]);
        }
        if(!$productId){
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
                'user_id' => Auth::user()->id, 'isPayed' => false, 'dateOfPayment' => Carbon::now(), 'status' => 0
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
            'message' => ($productOrder->quantity > 1 ? ucfirst(translate('items added')) : ucfirst(translate('item added')))
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
        $orderObj = Order::find($productOrderObj->order_id);
        $quantity = $productOrderObj->quantity;
        $response = $productOrderObj->delete();
        if(!$response){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('an error occured, try again please'))
            ]);
        }
        if($orderObj->getOrderProductsCount() < 1)
            $orderObj->delete();
        return json_encode([
            'success'     => true,
            'message'     => ($quantity > 1 ? ucfirst(translate('items removed')) : ucfirst(translate('item removed'))),
            'totalSum'    => Functions::formatMoney($orderObj->getSubTotal()),
            'hasProducts' => $orderObj->hasAnyProductOrder(Auth::user()->id)
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
            return redirect('/');
        }
        if(!$orderObj->getOrderProductsCount()){
            Functions::translateAndSetToSession('no products added to order', 'failure');
            return redirect('/');
        }
        $myOrderObj    = $orderObj->getOpenOrder($userObj->id);
        if(is_null($myOrderObj)){
            Functions::translateAndSetToSession('no active order found', 'failure');
            return redirect('/');
        }
        $productOrders = $myOrderObj->getProductOrders();
        $shipmentObj   = new Shipment();
        return view('authenticated/my_cart')->with([
            'order'         => $myOrderObj,
            'productOrder'  => $productOrders,
            'shipmentTypes' => $shipmentObj->getShipmentTypes()
        ]);
    }

    public function handleProductOrderQuantity()
    {
        $productOrderId = $this->getParameter('productOrderId');
        $operation      = $this->getParameter('operation');
        if(!$productOrderId || !$operation){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('some required data ara missing'))
            ]);
        }
        $avaliableOperations = ['+', '-'];
        if(!in_array($operation, $avaliableOperations)){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('unknown operation type'))
            ]);
        }
        $productOrderObj = ProductOrder::find($productOrderId);
        if(!$productOrderObj){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('product order id is invalid'))
            ]);
        }
        $orderObj = Order::where('id', $productOrderObj->order_id)->get()[0];
        $result = '';
        if($operation == '+'){
            $productOrderObj->increaseQuantityOfProduct();
        }else{
            $result = $productOrderObj->decreaseQuantityOfProduct();
        }
        $returnResponse = [
            'productPrice' => Functions::formatMoney($productOrderObj->getProduct()->price),
            'quantity'     => $productOrderObj->quantity,
            'totalSum'     => Functions::formatMoney($productOrderObj->totalSum),
            'subTotal'     => Functions::formatMoney($orderObj->getSubTotal())
        ];
        return json_encode([
            'success'    => true,
            'message'    => ($operation == '+' ? ucfirst(translate('product order updated')) : ucfirst(translate('product order removed')) ),
            'wasRemoved' => ($result == 'removed' ? true : false),
            'data'       => $returnResponse
        ]);
    }
}

?>