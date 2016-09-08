<?php
namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Tags
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Fact[] $facts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Estimation[] $estimations
 * @mixin \Eloquent
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