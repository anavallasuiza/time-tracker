<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model {
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