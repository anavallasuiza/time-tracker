<?php
namespace App\Models;

class Estimations extends \Eloquent {
    protected $table = 'estimations';
    protected $guarded = ['id'];

    public function activities()
    {
        return $this->belongsTo('App\Models\Activities', 'id_activities');
    }

    public function tags()
    {
        return $this->belongsTo('App\Models\Tags', 'id_tags');
    }
}