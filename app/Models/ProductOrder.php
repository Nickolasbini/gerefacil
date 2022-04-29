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
        $totalSum = $product->price * $request->quantity;
        $productOrderObj = $this::create([
            'product_id' => $product->id, 'user_id' => $order->user_id, 'quantity' => $request->quantity, 'totalSum' => $totalSum, 'order_id' => $order->id
        ]);
        if($productOrderObj instanceof $this)
            return true;
        return null;
    }

    public function updateQuantity($quantity = null)
    {
        if(!$quantity)
            return null;
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
}