<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLikes extends Model
{
	public $timestamps = true;
    protected $table = 'productlikes';

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
    ];

    // adds a like or remove by creating a record on table
    public function addLike($productId = null)
    {
        $object = $this::where('user_id', session()->get('authUser-id'))->where('product_id', $productId)->limit(1)->get();
        $userObj = \App\Models\User::find(session()->get('authUser-id'));
        $product = \App\Models\Product::find($productId);
        if(!$userObj || !$product){
            return null;
        } 
        if(count($object) > 0){
            $product->handleLikes(true);
            $object[0]->delete();
            return 'removed';
        }
        $this::create([
            'product_id' => $productId,
            'user_id'    => session()->get('authUser-id')
        ]);
        $product->handleLikes();
        return 'added';
    }
}
