<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Estimations
 *
 * @property-read \App\Models\Activities $activities
 * @property-read \App\Models\Tags $tags
 * @mixin \Eloquent
 */
class Estimations extends Model {
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