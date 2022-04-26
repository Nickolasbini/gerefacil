<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model {

	protected $table = 'favorite';
	public $timestamps = true;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    // return a list of favorites with product data embebed into favorite object
    public function list($page, $limit)
    {
        $favorites = $this->where('user_id', session()->get('authUser-id'))->paginate($limit);
        foreach($favorites as $favorite){
            $productObj = Product::find($favorite->product_id);
            $productId    = null;
            $productPhoto = null;
            $productName  = ucfirst(translate('not found'));
            if($productObj){
                $productId    = $productObj->id;
                $productName  = $productObj->name;
                $productPhoto = $productObj->getPhotoAsBase64();
            }
            $favorite->productId    = $productId;
            $favorite->productName  = $productName;
            $favorite->productPhoto = $productPhoto;
        }
        return $favorites;
    }
}