<?php

namespace App\Models;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMaster extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'user_master';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $hidden = [];

    protected $fillable = [
    	'id',
    	'first_name',
    	'last_name',
    	'account_secret_key',
    	'email',
    	'password',
    	'mobile_number',
        'xero_access_token',
        'tenant_id',
    	'last_login',
    	'is_active'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
	
}