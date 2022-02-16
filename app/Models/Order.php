<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

	protected $table = 'order';
	public $timestamps = true;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}