<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

	protected $table = 'category';
	public $timestamps = true;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
    ];

    // returns categories of this user or of template categories either as array of objects or with id as key and name
    public function getAll($onlyIdAndNames = false)
    {
        $userId = session()->get('authUser-id');
        $categories = Category::whereNull('user_id')->orWhere('user_id', $userId)->get();
        if(count($categories) < 1){
            return null;
        }
        if(!$onlyIdAndNames){
            return $categories;
        }
        $elements = [];
        foreach($categories as $category){
            $elements[$category->id] = $category->name;
        }
        return $elements;
    }
}