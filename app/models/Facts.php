<?php
namespace App\Models;

use Eloquent;

class Facts extends Eloquent {
    protected $table = 'facts';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function activities()
    {
        return $this->belongsTo('App\Models\Activities', 'id_activities');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\Users', 'id_users');
    }

    private function formatDate($date)
    {
        try {
            return new \DateTime($date);
        } catch (\Exception $e) {
            return new \DateTime('0000-00-00 00:00:00');
        }
    }

    public function getStartTimeAttribute($value)
    {
        return $this->formatDate($value);
    }

    public function getEndTimeAttribute($value)
    {
        return $this->formatDate($value);
    }
}