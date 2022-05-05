<?php

namespace App\Models;

use App\Helpers\Functions;
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
        'receiverAddress',
        'totalPrice',
        'status'
    ];

    const STATUS_ARRAY = [
        0 => 'empty order',
        1 => 'order created',
        2 => 'awaiting payment confirmation',
        3 => 'sent to delivery',
        4 => 'received by customer',
        5 => 'abandoned by user'
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
        $orderObj = Order::where('user_id', $userId)->where('isPayed', false)->where('status', '!=', '5')->get();
        if($orderObj->count() > 0)
            return $orderObj[0];
        return null;
    }

    public function getProductOrders()
    {
        $productOrder = new ProductOrder();
        return $productOrder->getProductsByOrderId($this->id);
    }

    public function getOrderProductsCount()
    {
        return ProductOrder::where('order_id', $this->id)->count();
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
        $result = $this->where('user_id', Auth::user()->id)->where('isPayed', false)->where('status', '!=', '5')->get();
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

    public function getAllMyOrders($userId, $status = null)
    {
        if($status)
            return Order::where('user_id', $userId)->where('status', '=', $status)->orderBy('status')->paginate(); 
        return Order::where('user_id', $userId)->orderBy('status')->paginate(10);
    }

    public function insertProductOrderInventoryToOrderObj($ordersArray)
    {
        foreach($ordersArray as $order){
            $details = $order->getDetailsOfProducts();
            $order->productDetails = $details;
        }
    }

    public function getDetailsOfProducts()
    {
        $products = $this->getProductOrders();
        $response = [];
        foreach($products as $productOrder){
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
                'productId'    => $productId,
                'productPhoto' => $productImg,
                'productName'  => $productName,
                'unitaryPrice' => $productPrice,
                'parcialSum'   => $productOrder->totalSum,
                'quantity'     => $productOrder->quantity,
                'updated_at'   => $productOrder->updated_at
            ];
        }
        return $response;
    }

    public function getAllStatusTranslated()
    {
        $response = [];
        $allStatus = $this::STATUS_ARRAY;
        foreach($allStatus as $statusNumber => $status){
            $response[$statusNumber] = ucfirst(translate($status));
        }
        return $response;
    }

    public function getStatusTranslated()
    {
        $allStatus = $this::STATUS_ARRAY;
        $status    = $this->status;
        if(!array_key_exists($status, $allStatus))
            return null;
        return ucfirst(translate($allStatus[$status]));
    }

    public function getNextStatusNumber()
    {
        $allStatus = $this::STATUS_ARRAY;
        $status    = $this->status + 1;
        if(!array_key_exists($status, $allStatus))
            return null;
        return $status;
    }

    public function getNextStatusTranslated()
    {
        $allStatus = $this::STATUS_ARRAY;
        $status    = $this->status + 1;
        if(!array_key_exists($status, $allStatus))
            return null;
        return ucfirst(translate($allStatus[$status]));
    }

    public function getStatusCorrespondentColor()
    {
        $status = $this->status;
        $correspondentColor = [
            0 => 'secondary',
            1 => 'info',
            2 => 'warning',
            3 => 'info',
            4 => 'success',
            5 => 'danger'
        ];
        if(!array_key_exists($status, $correspondentColor))
            return null;
        return $correspondentColor[$status];
    }

    public function getAllCustomersOrders($parameters = [], $limit = 10)
    {
        if(!is_array($parameters) || count($parameters) == 0)
            return Order::where('id', '>', '0')->paginate($limit);
        $status = (array_key_exists('status', $parameters) ? $parameters['status'] : null);
        $from   = (array_key_exists('from', $parameters)   ? $parameters['from']   : null);
        $to     = (array_key_exists('to', $parameters)     ? $parameters['to']     : null);

        if(!is_numeric($status))
            return Order::where('id', '>', '0')->paginate($limit);
        if($status && (!$from || !$to))
            return Order::where('id', '>', '0')->where('status', '=', $status)->paginate($limit);
        if(!$status && ($from && $to))
            return Order::where('id', '>', '0')->where('status', '=', $status)->whereBetween('updated_at', [$from, $to])->paginate($limit);
        if($status && $from && $to)
            return Order::where('id', '>', '0')->where('status', '=', $status)->whereBetween('updated_at', [$from, $to])->paginate($limit);
        return Order::where('status', '=', $status)->paginate($limit);
    }

    public function updateStatus($status = null)
    {
        $allStatus = $this::STATUS_ARRAY;
        $status    = ($status ? $status : $this->getNextStatusNumber());
        if(!array_key_exists($status, $allStatus))
            return false;
        $this->status = $status;
        return $this->save();
    }
}