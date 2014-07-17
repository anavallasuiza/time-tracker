<?php
namespace App\Models;

class Tags extends \Eloquent {
    protected $table = 'tags';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function facts()
    {
        return $this->belongsToMany('App\Models\Facts', 'facts_tags', 'id_tags', 'id_facts');
    }
}