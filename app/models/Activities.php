<?php
namespace App\Models;

use Eloquent;

class Activities extends Eloquent {
    protected $table = 'activities';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsTo('App\Models\Users', 'id_users');
    }

    public function facts()
    {
        return $this->hasMany('App\Models\Facts', 'id_facts', 'id');
    }
}