<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Facts
 *
 * @property-read \App\Models\Activities $activities
 * @property-read \App\Models\Users $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tags[] $tags
 * @property-read mixed $start_time
 * @property-read mixed $end_time
 * @mixin \Eloquent
 */
class Facts extends Model {
    use SoftDeletes;

    protected $table = 'facts';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

    public function activities()
    {
        return $this->belongsTo('App\Models\Activities', 'id_activities');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\Users', 'id_users');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tags', 'facts_tags', 'id_facts', 'id_tags');
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


    public static function filter($facts, $filters)
    {
        extract($filters);

        $I = \Auth::user();

        if ($user) {
            $facts->where('id_users', '=', $user);
        }

        if ($activity) {
            $facts->where('id_activities', '=', $activity);
        }

        if ($tag) {
            $facts->whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.id', '=', $tag);
            });
        }

        if ($first) {
            $facts->where('start_time', '>=', $first->format('Y-m-d 00:00:00'));
        }

        if ($last) {
            $facts->where('end_time', '<=', $last->format('Y-m-d 23:59:59'));
        }

        if ($description) {
            $facts->where('description', 'LIKE', '%'.$description.'%');
        }

        if ($client) {
            $facts->whereHas('activities', function ($query) use ($client) {
                if($client==-1)
                {
                    $query->whereNull('activities.id_clients');
                }else{
                    $query->where('activities.id_clients', '=', $client);
                }
            });
        }

        list($sort_field, $sort_mode) = explode('-', $sort);

        $facts->orderBy($sort_field.'_time', $sort_mode);

        return $facts;
    }
}