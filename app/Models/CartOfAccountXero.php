<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartOfAccountXero extends Model
{
    use HasFactory;
    protected $table = 'xero_char_of_account';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $hidden = [];

    protected $fillable = [
    	'id',
    	'code',
    	'name',
    	'type',
    	'description',
    	'is_active'
    ];
}
