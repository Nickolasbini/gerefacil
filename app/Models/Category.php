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
}