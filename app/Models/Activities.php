<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activities extends Model {
    protected $table = 'activities';
    protected $guarded = ['id'];

    public function client()
    {
        return $this->belongsTo(Clients::class, 'id_clients');
    }

    public function facts()
    {
        return $this->hasMany('App\Models\Facts', 'id_activities', 'id');
    }

    public function estimations()
    {
        return $this->hasMany('App\Models\Estimations', 'id_activities', 'id');
    }
}