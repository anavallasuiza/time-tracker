<?php
namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Activities
 *
 * @property-read \App\Database\Models\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Fact[] $facts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Estimation[] $estimations
 * @mixin \Eloquent
 */
class Activity extends Model {
    protected $table = 'activities';
    protected $guarded = ['id'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_clients');
    }

    public function facts()
    {
        return $this->hasMany(Fact::class, 'id_activities', 'id');
    }

    public function estimations()
    {
        return $this->hasMany(Estimation::class, 'id_activities', 'id');
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