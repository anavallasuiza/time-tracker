<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tags
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Facts[] $facts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Estimations[] $estimations
 * @mixin \Eloquent
 */
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