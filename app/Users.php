<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable
{
    use SoftDeletes;
    protected $table = 'users';

    protected $hidden = array('password','remember_token');
    
    public function types(){
        return $this->hasOne('App\Users_Types','id','type');
    }
}
