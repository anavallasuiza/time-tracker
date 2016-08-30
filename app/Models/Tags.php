<?php
namespace App\Models;

class Tags extends \Eloquent {
    protected $table = 'tags';
    protected $guarded = ['id'];

    public function facts()
    {
        return $this->belongsToMany('App\Models\Facts', 'facts_tags', 'id_tags', 'id_facts');
    }

    public function estimations()
    {
        return $this->hasMany('App\Models\Estimations', 'id_tags', 'id');
    }
}