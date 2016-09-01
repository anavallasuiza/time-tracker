<?php
namespace App\Models;

/**
 * App\Models\Logs
 *
 * @property-read \App\Models\Users $users
 * @mixin \Eloquent
 */
class Logs extends \Eloquent {
    protected $table = 'logs';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsTo('App\Models\Users', 'id_users');
    }
}