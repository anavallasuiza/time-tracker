<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Activities
 *
 * @property-read \App\Models\Clients $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Facts[] $facts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Estimations[] $estimations
 * @mixin \Eloquent
 */
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

    /**
     * @return bool
     */
    public function isArchived()
    {
        return $this->archived==0?false:true;
    }

    /**
     * @return bool
     */
    public function hasClient()
    {
        return !empty($this->id_clients);
    }

}