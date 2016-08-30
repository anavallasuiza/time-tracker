<?php
namespace App\Models;

use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Users extends Eloquent implements AuthenticatableContract, CanResetPasswordContract {
    use Authenticatable, CanResetPassword;

    protected $table = 'users';
    protected $hidden = array('password', 'password_tmp');
    protected $guarded = ['id'];
    protected $dates = [];

    public $timestamps = false;

    public function facts()
    {
        return $this->hasMany('App\Models\Facts', 'id_users', 'id');
    }
}