<?php
namespace App\Models;

/**
 * App\Models\Estimations
 *
 * @property-read \App\Models\Activities $activities
 * @property-read \App\Models\Tags $tags
 * @mixin \Eloquent
 */
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