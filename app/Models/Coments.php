<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coments extends Model {

	protected $table = 'comments';
	public $timestamps = true;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'commentText',
        'product_id',
        'user_id',
    ];
}