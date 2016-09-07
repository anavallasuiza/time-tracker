<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * App\Models\Users
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Facts[] $facts
 * @mixin \Eloquent
 */
class Users extends Model implements AuthenticatableContract, CanResetPasswordContract {
    use Authenticatable, CanResetPassword;

    protected $table = 'users';
    protected $hidden = array('password', 'password_tmp');
    protected $guarded = ['id'];
    protected $dates = [];

    public function facts()
    {
        return $this->hasMany('App\Models\Facts', 'id_users', 'id');
    }
}