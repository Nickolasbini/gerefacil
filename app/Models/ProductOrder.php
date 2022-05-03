<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Order;

class ProductOrder extends Model {

	protected $table = 'product_order';
	public $timestamps = true;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'totalSum'
    ];
    
    // add a product to sent order
    public function addProduct(Product $product = null, Order $order = null, $request = null)
    {
        if(!$product || !$order || !$request)
            return null;
        $object = $this->getProductOrderByProductId($product->id, $order->id);
        if($object->count() > 0){
            $productOrderObj = $object[0];
            $quantity = $productOrderObj->quantity + 1;
            $productOrderObj->updateQuantity($quantity);
            return true;
        }
        $quantity = $request->quantity;
        $quantity = ($quantity ? $quantity : 1);
        $totalSum = $product->price * $quantity;
        $productOrderObj = $this::create([
            'product_id' => $product->id, 'user_id' => $order->user_id, 'quantity' => $quantity, 'totalSum' => $totalSum, 'order_id' => $order->id
        ]);
        if($productOrderObj instanceof $this)
            return true;
        return null;
    }

    // updates the quantity of a productOrder
    public function updateQuantity($quantity = null)
    {
        $quantity = (!$quantity ? $this->quantity + 1 : $quantity);
        $productObj = Product::find($this->product_id);
        $totalSum = $productObj->price * $quantity;
        $this->totalSum = $totalSum;
        $this->quantity = $quantity;
        $result = $this->save();
        if($result)
            return $totalSum;
        return null;
    }

    public function decreaseQuantityOfProduct()
    {
        $quantity = $this->quantity;
        $quantity = $quantity - 1;
        if($quantity == 0){
            $this->delete();
            return 'removed';
        }
        $productObj = Product::find($this->product_id);
        $totalSum = $productObj->price * $quantity;
        $this->totalSum = $totalSum;
        $this->quantity = $quantity;
        $result = $this->save();
        if($result)
            return $totalSum;
        return null;
    }

    public function increaseQuantityOfProduct()
    {
        $quantity = $this->quantity;
        $quantity = $quantity + 1;
        $productObj = Product::find($this->product_id);
        $totalSum = $productObj->price * $quantity;
        $this->totalSum = $totalSum;
        $this->quantity = $quantity;
        $result = $this->save();
        if($result)
            return $totalSum;
        return null;
    }

    public function getProductsByOrderId($orderId = null)
    {
        return $this->where('order_id', $orderId)->get();
    }

    // adds product to $this object
    public function addProductObject()
    {
        $this->product = Product::find($this->product_id);
        return $this->product;
    }

    public function getProductFromProductOrder()
    {
        $productId = $this->product_id;
        return Product::find($productId);
    }

    public function getProduct()
    {
        return Product::find($this->product_id);
    }

    public function getProductOrderByProductId($productId = null, $orderId = null)
    {
        return ProductOrder::where('product_id', $productId)->where('order_id', $orderId)->get();
    }
}