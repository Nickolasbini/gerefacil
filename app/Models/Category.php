<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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

    /*public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'id_user');
    }*/

    public function getUser()
    {
        return User::find($this->user_id);
    }

    // return all categories avaliable for this user
    public function getMyCategories()
    {
        $allCategories = Category::where('user_id', session()->get('authUser-id'))->orWhere('user_id', session()->get('masterAdmin-id'))->get();
        return $allCategories;
    }
}