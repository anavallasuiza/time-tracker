<?php
namespace App\Models;

/**
 * App\Models\Activities
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Facts[] $facts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Estimations[] $estimations
 * @mixin \Eloquent
 */
class Activities extends \Eloquent {
    protected $table = 'activities';
    protected $guarded = ['id'];

    public function facts()
    {
        return $this->hasMany('App\Models\Facts', 'id_activities', 'id');
    }

    public function estimations()
    {
        return $this->hasMany('App\Models\Estimations', 'id_activities', 'id');
    }
}