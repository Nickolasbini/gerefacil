<?php

namespace App\Http\Controllers;

use App\Helpers\Functions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductOrder;
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
     * Remove 
     * @param  id   to remove
     * 
     * @return
    */
    public function remove()
    {
        
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
}

?>