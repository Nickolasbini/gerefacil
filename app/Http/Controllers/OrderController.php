<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Create or Update
     * @param  <int> id for update
     * 
     * @return 
    */
    public function closeOrder()
    {
        $orderId         = $this->getParameter('orderId');
        $orderPrice      = $this->getParameter('orderPrice');
        // $isPayed         = $this->getParameter('isPayed');
        // $dateOfPayment   = $this->getParameter('dateOfPayment');
        $numberOfUnits   = $this->getParameter('numberOfUnits');
        $shippingPrice   = $this->getParameter('shippingPrice');
        $receiverAddress = $this->getParameter('receiverAddress');

        if(!$orderPrice || !$numberOfUnits || !$shippingPrice || !$receiverAddress){
            Functions::translateAndSetToSession('required data missing', 'failure');
            return redirect()->back();
        }
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
     * abandoms an order canceling it and leaving a history 
     * @param  id   to remove
     * 
     * @return
    */
    public function abandomOrder()
    {
        $orderId = $this->getParameter('orderId');
        if(!$orderId){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('required data missing'))
            ]);
        }
        $orderObj = Order::find($orderId);
        if(!$orderObj){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('order is invalid'))
            ]);
        }
        $orderObj->status = 5;
        $result = $orderObj->save();
        if(!$result){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('an error occured, try again please'))
            ]);
        }
        return json_encode([
            'success' => true,
            'message' => ucfirst(translate('order was abandoned'))
        ]);
    }

    public function calculateOrderShipmentPriceAndDelivery()
    {
        $orderId      = $this->getParameter('orderId');
        $shipmentType = $this->getParameter('shipmentType');
        $deliveryCEP  = $this->getParameter('deliveryCEP', Auth::user()->cep);
        $orderObj = Order::find($orderId);
        if(!$orderObj)
            return null;
        $aShipmentData = $orderObj->getSumOfShipmentSpecificationsOnOrderProducts();
        $sellerCEP    = $orderObj->getSellerCEP();
        $shipment = new \App\Models\Shipment(
            $deliveryCEP, 
            $sellerCEP, 
            $aShipmentData['weight'],
            $aShipmentData['length'], 
            $aShipmentData['width'], 
            $aShipmentData['height'], 
            $orderObj->getSubTotal(),
            $shipmentType
        );
        $shipmentData = [
            'value'        => Functions::formatMoney($shipment->getValor()),
            'deliveryTime' => $shipment->getPrazoEntrega()
        ];
        return json_encode([
            'success' => true,
            'message' => 'response',
            'content' => $shipmentData,
            'total'   => Functions::formatMoney($orderObj->getSubTotal() + $shipment->getValor())
        ]);
    }

    // fetches detail of an order such as its total and partial values 
    public function orderDetail()
    {
        $orderId = $this->getParameter('orderId');
        if(!$orderId){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('required data missing'))
            ]);
        }
        $orderObj = Order::find($orderId);
        if(!$orderObj){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('order is invalid'))
            ]);
        }
        dd($orderObj->getSubTotal());
    }

    public function pay()
    {
        $orderId      = $this->getParameter('orderId');
        $shipmentType = $this->getParameter('shipmentType', 41106);
        if(!$orderId || !$shipmentType){
            Functions::translateAndSetToSession('required data missing', 'failure');
            return redirect('/');
        }
        $orderObj = Order::find($orderId);
        if(!$orderObj || $orderObj->user_id != $this->getLoggedUserId()){
            Functions::translateAndSetToSession('order is invalid', 'failure');
            return redirect('/');
        }
        $user = Auth::user();
        if(!$user->address){
            Functions::translateAndSetToSession('please enter your addess', 'failure');
            return redirect('user/profile');
        }
        
        $orderObj->dateOfPayment   = Carbon::now();
        $orderObj->shippingPrice   = $orderObj->getOrderShipmentPrice($shipmentType, $user);
        $orderObj->orderPrice      = $orderObj->getSubTotal();
        $orderObj->totalPrice      = $orderObj->shippingPrice + $orderObj->orderPrice;
        $orderObj->isPayed         = true;
        $orderObj->receiverAddress = Auth::user()->address;
        $orderObj->status          = 2;
        $result = $orderObj->save();
        if(!$result){
            Functions::translateAndSetToSession('an error occurred, try agai please', 'failure');
            return redirect('cart');
        }
        Functions::translateAndSetToSession('payment sent, please await for the product(s) to be sent', 'success');
        return redirect('dashboard/sale');
    }

    // the view with Orders
    public function listAdmCartList()
    {
        $selectedStatus = $this->getParameter('status', '');
        $orderObj = new Order();
        $orders = $orderObj->getAllMyOrders($this->getLoggedUserId(), $selectedStatus);
        $orderObj->insertProductOrderInventoryToOrderObj($orders);
        return view('dashboard/order_views/order_home')->with([
            'orders'         => $orders,
            'status'         => $orderObj->getAllStatusTranslated(),
            'selectedStatus' => $selectedStatus
        ]);
    }

    // get number of products in the current active order
    public function getNumberOfProductsOnOrder()
    {
        $orderObj = new Order();
        $order = $orderObj->getOpenOrder($this->getLoggedUserId());
        if(is_null($order)){
            return json_encode([
                'success' => false,
                'number'  => 0
            ]);
        }
        $total = $order->getOrderProductsCount();
        return json_encode([
            'success' => true,
            'number'  => $total
        ]);
    }

    // change the status of sent Order id to sent status
    public function updateStatus()
    {
        $orderId = $this->getParameter('orderId');
        $status  = $this->getParameter('status');
        if(!$orderId || !$status){
            return json_encode([
                'success' => false,
                'message' => 'missing parameters',
                'status'  => null
            ]);
        }
        $orderObj = Order::find($orderId);
        if(!$orderObj){
            return json_encode([
                'success' => false,
                'message' => 'order not found',
                'status'  => null
            ]);
        }
        if($status != $orderObj->getNextStatusNumber()){
            return json_encode([
                'success' => false,
                'message' => 'invalid status',
                'status'  => null
            ]);
        }
        $result = $orderObj->updateStatus($status);
        if(!$result){
            return json_encode([
                'success' => false,
                'message' => ucfirst(translate('an error occured, try again please')),
                'status'  => null
            ]);
        }
        return json_encode([
            'success' => false,
            'message' => ucfirst(translate('order status updated')),
            'status'  => $orderObj->status
        ]);
    }
}

?>