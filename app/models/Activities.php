<?php
namespace App\Models;

class Activities extends \Eloquent {
    protected $table = 'activities';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function facts()
    {
        return $this->hasMany('App\Models\Facts', 'id_facts', 'id');
    }
}