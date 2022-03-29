<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

	protected $table = 'product';
	public $timestamps = true;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'productDetails',
        'price',
        'quantity',
        'user_id',
        'document'
    ];

    // setters
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setCategory($categoryId)
    {
        $this->category_id = $categoryId;
    }
    public function setProductDetails($productDetails)
    {
        $this->productDetails = $productDetails;
    }
    public function setPrice($price)
    {
        $this->price = $price;
    }
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    public function setUser($userId)
    {
        $this->user_id = $userId;
    }

    // return product image
    public function getPhotoAsBase64()
    {
        $documentObj = (new Document)->find($this->document);
        if(!$documentObj){
            return null;
        }
        $path = $documentObj->parsePath();
        if(file_exists($path)){
            $fileBase64 = file_get_contents($path);
            return 'data:image/png;base64,'.base64_encode($fileBase64);
        }
        return null;
    }
}