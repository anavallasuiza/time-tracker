<?php
namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Estimations
 *
 * @property-read \App\Database\Models\Activity $activities
 * @property-read \App\Database\Models\Tag $tags
 * @mixin \Eloquent
 * @property string $deleted_at
 * @property integer $id
 * @property integer $hours
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $id_activities
 * @property integer $id_tags
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Estimation whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Estimation whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Estimation whereHours($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Estimation whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Estimation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Estimation whereIdActivities($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Estimation whereIdTags($value)
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