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
        'document',
        'likes',
        'weightInKM',
        'lengthInCentimeter',
        'widthInCentimeter',
        'heightInCentimeter'
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

    // adds 1 or removes 1 to total of likes of this product
    public function handleLikes($remove = false)
    {
        $totalOfLikes = $this->likes;
        $totalOfLikes = $remove ? $totalOfLikes-- : $totalOfLikes++;
        $this->likes  = $totalOfLikes;
        $this->save();
        return $this;
    }

    // calculates the specifications of a product
    public function calculateSpecificationsCubicSizeOfProductOrder(ProductOrder $productsArray)
    {
        $totalWeight = 0;
        $totalCubicCentimeters = 0;
        foreach ($productsArray as $productOrder) {
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

    // calculates the specifications of a product
    public function calculateSpecificationsCubicSizeOfProduct(Product $productsArray, $quantity = 1)
    {
        $productsArray = (is_array($productsArray) ? $productsArray : [$productsArray]);
        $totalWeight = 0;
        $totalCubicCentimeters = 0;
        foreach ($productsArray as $aProduct) {
            $weight = $aProduct->weightInKM * $quantity;
            $width  = ($aProduct->heightInCentimeter * $aProduct->lengthInCentimeter * $aProduct->widthInCentimeter) * $quantity;
            $totalWeight           += $weight;
            $totalCubicCentimeters += $width; 
        }
        $cubeRoot = round(pow($totalCubicCentimeters, 1/3), 2);
        $mathResult = [
            'totalWeight' => $totalWeight,
            'cubeRoot'    => $cubeRoot
        ];
        $length   = $cubeRoot    < 16  ? 16  : $cubeRoot;
        $height   = $cubeRoot    < 2   ? 2   : $cubeRoot;
        $width    = $cubeRoot    < 11  ? 11  : $cubeRoot;
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
}