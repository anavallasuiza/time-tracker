<?php
namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * App\Database\Models\Users
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Fact[] $facts
 * @mixin \Eloquent
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
    use Authenticatable, CanResetPassword;

    protected $table = 'users';
    protected $hidden = array('password', 'password_tmp');
    protected $guarded = ['id'];
    protected $dates = [];

    public function facts()
    {
        return $this->hasMany(Fact::class, 'id_users', 'id');
    }
}