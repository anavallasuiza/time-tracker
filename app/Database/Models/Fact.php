<?php
namespace App\Database\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Database\Models\Facts
 *
 * @property-read \App\Database\Models\Activity $activities
 * @property-read \App\Database\Models\User $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Tag[] $tags
 * @property mixed $start_time
 * @property mixed $end_time
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $total_time
 * @property string $description
 * @property string $hostname
 * @property integer $remote_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property integer $id_activities
 * @property integer $id_users
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereEndTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereTotalTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereHostname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereRemoteId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereIdActivities($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Fact whereIdUsers($value)
 */
class Fact extends Model {
    use SoftDeletes;

    protected $table = 'facts';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

    public function activities()
    {
        return $this->belongsTo(Activity::class, 'id_activities');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'facts_tags', 'id_facts', 'id_tags');
    }

    private function formatDate($date)
    {
        try {
            return new DateTime($date);
        } catch (\Exception $e) {
            return new DateTime('0000-00-00 00:00:00');
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