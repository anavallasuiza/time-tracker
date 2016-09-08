<?php
namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Tags
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Fact[] $facts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Estimation[] $estimations
 * @mixin \Eloquent
 * @property integer $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Tag whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Tag whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Tag whereUpdatedAt($value)
 */
class Tag extends Model {
    protected $table = 'tags';
    protected $guarded = ['id'];

    public function facts()
    {
        return $this->belongsToMany(Fact::class, 'facts_tags', 'id_tags', 'id_facts');
    }

    public function estimations()
    {
        return $this->hasMany(Estimation::class, 'id_tags', 'id');
    }
}