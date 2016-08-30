<?php
namespace App\Models;

class Logs extends \Eloquent {
    protected $table = 'logs';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsTo('App\Models\Users', 'id_users');
    }
}