<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function getSumOfShipmentSpecificationsOnOrderProducts()
    {
        $data = [
            'weight' => 0.0,
            'length' => 0,
            'width'  => 0,
            'height' => 0
        ];
        $products = $this->getProductOrders();
        if($products->count() < 1)
            return $data;
        foreach($products as $productOrder){
            $aProduct = Product::find($productOrder->product_id);
            $data['weight'] = $data['weight'] + ($aProduct->weightInKM * $productOrder->quantity);
            $data['length'] = $data['length'] + ($aProduct->lengthInCentimeter * $productOrder->quantity);
            $data['width']  = $data['width']  + ($aProduct->widthInCentimeter  * $productOrder->quantity);
            $data['height'] = $data['height']  + ($aProduct->heightInCentimeter * $productOrder->quantity);
        }
        return $data;
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
}