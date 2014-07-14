<?php
namespace App\Models;

use Eloquent;

class Facts extends Eloquent {
    protected $table = 'facts';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function activities()
    {
        return $this->belongsTo('App\Models\Activities', 'id_activities');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\Users', 'id_users');
    }
}