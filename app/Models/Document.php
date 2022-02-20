<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class document extends Model
{
    protected $table = 'document';
	public $timestamps = true;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filePath',
        'fileName',
        'extension',
        'miniaturesJSON',
    ];
}
