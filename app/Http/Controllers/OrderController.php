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
        $orderObj = Order::find($orderId);
        if(!$orderObj)
            return null;
        $data      = $orderObj->getSumOfShipmentSpecificationsOnOrderProducts();
        $sellerCEP = $orderObj->getSellerCEP();
        $cepData = [
            'value'       => null,
            'deliverTime' => null
        ];
        $shipment = new \App\Models\Shipment(
            Auth::user()->id, 
            $sellerCEP, 
            $data['weight'],
            $data['length'], 
            $data['width'], 
            $data['height'], 
            $orderObj->getSubTotal(),
            $shipmentType
        );
        $cepData = [
            'value'       => Functions::formatMoney($shipment->getValor()),
            'deliverTime' => $shipment->getPrazoEntrega()
        ];
        return json_encode([
            'success' => true,
            'message' => 'response',
            'content' => $cepData
        ]);
    }
}

?>