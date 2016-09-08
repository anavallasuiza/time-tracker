<?php
namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Logs
 *
 * @property-read \App\Database\Models\User $users
 * @mixin \Eloquent
 */
class Log extends Model {
    protected $table = 'logs';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsTo(User::class, 'id_users');
    }
}