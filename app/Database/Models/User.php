<?php
namespace App\Database\Models;

use ANavallaSuiza\Laravel\Database\Contracts\Repository\HasCustomRepository;
use App\Database\Repositories\UserRepository;
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
 * @property integer $id
 * @property string $name
 * @property string $user
 * @property string $email
 * @property string $password
 * @property string $password_token
 * @property string $remember_token
 * @property string $api_key
 * @property boolean $store_hours
 * @property boolean $admin
 * @property boolean $enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereUser($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User wherePasswordToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereApiKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereStoreHours($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereAdmin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereEnabled($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\User whereUpdatedAt($value)
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasCustomRepository {
    use Authenticatable, CanResetPassword;

    protected $table = 'users';
    protected $hidden = array('password', 'password_tmp');
    protected $guarded = ['id'];
    protected $dates = [];

    public function facts()
    {
        return $this->hasMany(Fact::class, 'id_users', 'id');
    }

    public function isAdmin()
    {
        return $this->admin;
    }

    public function getDateFormatConfig()
    {
       return 'd/m/Y'.($this->store_hours ? ' H:i' : '');
    }

    public function repository()
    {
        return UserRepository::class;
    }

}