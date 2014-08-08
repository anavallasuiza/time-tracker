<?php
namespace App\Models;

use App\Libs;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Facts extends \Eloquent {
    use SoftDeletingTrait;

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

    public static function filter($filters)
    {
        extract($filters);

        $I = \Auth::user();

        $facts = self::with(['activities'])->with(['users']);

        if (empty($I->admin)) {
            $facts->where('id_users', '=', $I->id);
        } elseif (isset($user) && (int)$user) {
            $facts->where('id_users', '=', (int)$user);
        }

        if (isset($activity) && (int)$activity) {
            $facts->where('id_activities', '=', (int)$activity);
        }

        if (isset($tag) && (int)$tag) {
            if (isset($tag_unique) && $tag_unique) {
                $facts->with(['tags' => function($query) use ($tag) {
                    $query->where('tags.id', '=', (int)$tag);
                }]);
            } else {
                $facts->join('facts_tags', 'facts_tags.id_facts', '=', 'facts.id')
                    ->where('facts_tags.id_tags', '=', (int)$tag);
            }
        } else {
            $facts->with(['tags']);
        }

        if (isset($first) && $first && ($filters['first'] = Libs\Utils::checkdate($first, 'd/m/Y'))) {
            $facts->where('end_time', '>=', $filters['first']->format('Y-m-d 00:00:00'));
        } else {
            $filters['first'] = null;
        }

        if (isset($last) && $last && ($filters['last'] = Libs\Utils::checkdate($last, 'd/m/Y'))) {
            $facts->where('end_time', '<=', $filters['last']->format('Y-m-d 23:59:59'));
        } else {
            $filters['last'] = null;
        }

        if (isset($description) && $description) {
            $facts->where('description', 'LIKE', '%'.$description.'%');
        }

        $filters['sort'] = empty($sort) ? 'end-desc' : $sort;

        list($sort_field, $sort_mode) = explode('-', $filters['sort']);

        if (in_array($sort_field, ['start', 'end', 'total'], true)) {
            $facts->orderBy($sort_field.'_time', ($sort_mode === 'asc') ? 'ASC' : 'DESC');
        } else {
            $facts->orderBy('end_time', 'DESC');
        }

        return [$facts, $filters];
    }
}