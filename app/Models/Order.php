<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model {

	protected $table = 'order';
	public $timestamps = true;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'orderPrice',
        'isPayed',
        'dateOfPayment',
        'user_id',
        'numberOfUnits',
        'shippingPrice',
        'receiverAddress'
    ];

    const STATUS_ARRAY = [
        0 => 'order',
        1 => 'awaiting payment confirmation',
        2 => 'sent to delivery'
    ];

    // return <bool> true or false in case there is a order of mine (not payed)
    public function haveAnyNonPayedOrder($userId = null)
    {
        $result = Order::where('user_id', $userId)->where('isPayed', false)->count();
        return ($result > 0 ? true : false);
    }

    // return <bool> true or false in case there is a order of mine (not payed)
    public function haveAnyPayedOrder($userId = null)
    {
        $result = Order::where('user_id', $userId)->where('isPayed', true)->count();
        return ($result > 0 ? true : false);
    }

    public function getOpenOrder($userId = null)
    {
        return Order::where('user_id', $userId)->where('isPayed', false)->get()[0];
    }

    public function getProductOrders()
    {
        $productOrder = new ProductOrder();
        return $productOrder->getProductsByOrderId($this->id);
    }

    public function getSubTotal()
    {
        $allProductsInOrder = $this->getProductOrders();
        $totalPrice = 0;
        foreach($allProductsInOrder as $productOrder){
            $totalPrice = $totalPrice + $productOrder->totalSum;
        }
        return $totalPrice;
    }

    // totalizes values used on shipment, such as: 'weight', 'length', 'width' and 'height'
    public function getSumOfShipmentSpecificationsOnOrderProducts($productOrderId = null)
    {
        $data = [
            'weight' => 0.0,
            'length' => 0,
            'width'  => 0,
            'height' => 0
        ];
        if($productOrderId){
            $products = ProductOrder::where('id', $productOrderId)->get();
        }else{
            $products = $this->getProductOrders();
        }
        if($products->count() < 1)
            return $data;
        $productObj = new Product();
        return $productObj->calculateSpecificationsCubicSizeOfProductOrder($products);
    }

    public function getSellerCEP()
    {
        $products = $this->getProductOrders();
        if($products->count() < 1)
            return null;
        $productObj = Product::find($products[0]->product_id);
        if(!$productObj)
            return null;
        $productOwnerUser = User::find($productObj->user_id);
        return $productOwnerUser->cep;
    }

    public function hasAnyProductOrder($userId = null)
    {
        $order = Order::where('user_id', $userId)->where('isPayed', false)->get();
        if($order->count() < 1)
            return false;
        if(ProductOrder::where('order_id', $order[0]->id)->count() > 0)
            return true;
        return false;
    }

    public function getIdOfOpenActiveOrder()
    {
        if(!Auth::user())
            return null;
        $result = $this->where('user_id', Auth::user()->id)->where('isPayed', false)->get();
        return ($result->count() > 0 ? $result[0]->id : null);
    }

    public function getOrderShipmentPrice($shipmentType, $user)
    {
        $aShipmentData = $this->getSumOfShipmentSpecificationsOnOrderProducts();
        $sellerCEP     = $this->getSellerCEP();
        $shipment = new \App\Models\Shipment(
            $user->cep, 
            $sellerCEP, 
            $aShipmentData['weight'],
            $aShipmentData['length'], 
            $aShipmentData['width'], 
            $aShipmentData['height'], 
            $this->getSubTotal(),
            $shipmentType
        );
        return $shipment->getValor();
    }
}