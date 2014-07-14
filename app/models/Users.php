<?php
namespace App\Models;

use Eloquent;

class Users extends Eloquent {
    protected $table = 'users';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function facts()
    {
        return $this->hasMany('App\Models\Facts', 'id_users', 'id');
    }
}