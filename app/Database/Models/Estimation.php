<?php
namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Estimations
 *
 * @property-read \App\Database\Models\Activity $activities
 * @property-read \App\Database\Models\Tag $tags
 * @mixin \Eloquent
 */
class Estimation extends Model {
    protected $table = 'estimations';
    protected $guarded = ['id'];

    public function activities()
    {
        return $this->belongsTo(Activity::class, 'id_activities');
    }

    public function tags()
    {
        return $this->belongsTo(Tag::class, 'id_tags');
    }
}