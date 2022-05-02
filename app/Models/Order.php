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
        $totalWeight = 0;
        $totalCubicCentimeters = 0;
        foreach ($products as $productOrder) {
            $aProduct = Product::find($productOrder->product_id);
            $weight = $aProduct->weightInKM * $productOrder->quantity;
            $width  = ($aProduct->heightInCentimeter * $aProduct->lengthInCentimeter * $aProduct->widthInCentimeter) * $productOrder->quantity;
            $totalWeight           += $weight;
            $totalCubicCentimeters += $width; 
        }
        $cubeRoot = round(pow($totalCubicCentimeters, 1/3), 2);
        $mathResult = [
            'totalWeight' => $totalWeight,
            'cubeRoot'    => $cubeRoot
        ];
        $length   = $cubeRoot < 16 ? 16 : $cubeRoot;
        $height   = $cubeRoot < 2 ? 2 : $cubeRoot;
        $width    = $cubeRoot < 11 ? 11 : $cubeRoot;
        $weight   = $totalWeight < 0.3 ? 0.3 : $totalWeight;
        $diameter = hypot($length, $width); // just do it if the thing is in a rectangle shape
        $result = [
            'weight' => $weight,
            'length' => $length,
            'height' => $height,
            'width'  => $width,
            //'nVlDiametro' => $diameter
        ];
        return $result;
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
        $order = Order::where('user_id', $userId)->get();
        if($order->count() < 1)
            return false;
        if(ProductOrder::where('order_id', $order[0]->id)->count() > 0)
            return true;
        return false;
    }

    public function getIdOfActiveOrder()
    {
        if(!Auth::user())
            return null;
        $result = $this->where('user_id', Auth::user()->id)->get();
        return ($result->count() > 0 ? $result[0]->id : null);
    }
}