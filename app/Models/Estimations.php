<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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