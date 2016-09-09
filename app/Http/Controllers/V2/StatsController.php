<?php


namespace App\Http\Controllers\V2;

use App\Database\Models\Estimation;
use App\Database\Models\Fact;
use Datetime;
use Illuminate\Support\Collection;
use App\Libs\Utils;
use Input;

class StatsController extends BaseController
{
    public function index()
    {
        $first = Input::get('first');

        if (empty($first)) {
            $first = date('d/m/Y', strtotime('-1 month'));
        }

        $filters = Utils::filters([
            'first' => $first
        ]);

        /** @var Collection $facts */
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $facts = Fact::select('id_activities')->groupBy('id_activities');
        /** @var Collection $factsFiltered */
        $factsFiltered = Fact::filter($facts, $filters)->get();

        $ids = Utils::objectColumn($factsFiltered, 'id_activities') ?: [0];

        $newFilters = $filters;

        if (empty($filters['times'])) {
            $newFilters = $filters;
            $newFilters['first'] = false;
        }

        /** @var Fact $facts */
        $facts = Fact::whereIn('id_activities', $ids);

        /** @var Fact $factsFiltered */
        $factsFiltered = Fact::filter($facts, $newFilters);
        $factsFiltered
            ->with(['activities'])
            ->with(['users']);

        if ($filters['tag']) {
            $facts->with(['tags' => function (/** @var Fact $query */$query) use ($filters) {
                $query->where('tags.id', '=', $filters['tag']);
            }]);
        } else {
            $facts->with(['tags']);
        }

        $facts = $facts->get();

        $tmp = Estimation::whereIn('id_activities', $ids);

        if ($filters['tag']) {
            $tmp->where('id_tags', '=', $filters['tag']);
        }

        $tmp = $tmp->get();
        $estimations = [];

        foreach ($tmp as $st) {
            foreach (['activities', 'tags'] as $c) {
                if (empty($estimations[$c][$st->{'id_'.$c}])) {
                    $estimations[$c][$st->{'id_'.$c}] = 0;
                }

                $estimations[$c][$st->{'id_'.$c}] += $st->hours;
            }
        }

        unset($tmp, $c, $st);

        $activities = $tags = $users = [];

        foreach ($facts as $fact) {
            if (!array_key_exists($fact->activities->id, $activities)) {
                $activities[$fact->activities->id] = [
                    'id' => $fact->activities->id,
                    'name' => $fact->activities->name,
                    'time' => 0,
                    'total_hours' => 0,
                    'selected' => ($filters['activity'] == $fact->activities->id)
                ];
            }

            if (isset($estimations['activities'][$fact->activities->id])) {
                $activities[$fact->activities->id]['total_hours'] = $estimations['activities'][$fact->activities->id];
            }

            $activities[$fact->activities->id]['time'] += $fact->total_time;

            foreach ($fact->tags as $tag) {
                if (!array_key_exists($tag->id, $tags)) {
                    $tags[$tag->id] = [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'time' => 0,
                        'total_hours' => 0,
                        'selected' => ($filters['tag'] == $tag->id)
                    ];
                }

                if (isset($estimations['tags'][$tag->id])) {
                    $tags[$tag->id]['total_hours'] = $estimations['tags'][$tag->id];
                }

                $tags[$tag->id]['time'] += $fact->total_time;
            }

            if (empty($this->getLoggedUser()->isAdmin()) && ($fact->users->id !== $this->getLoggedUser()->id)) {
                continue;
            }

            if (!array_key_exists($fact->users->id, $users)) {
                $users[$fact->users->id] = [
                    'id' => $fact->users->id,
                    'name' => $fact->users->name,
                    'time' => 0,
                    'total_hours' => 0,
                    'selected' => ($filters['user'] == $fact->users->id)
                ];
            }

            $users[$fact->users->id]['time'] += $fact->total_time;
        }

        foreach (['activities', 'tags', 'users'] as $stats) {
            $contents = &$$stats;

            if (empty($contents)) {
                continue;
            }

            $max = array_sum(array_column($contents, 'time'));

            array_walk($contents, function (&$value) use ($max) {
                $value['percent'] = round(($value['time'] * 100) / $max);

                if (empty($value['total_hours'])) {
                    $value['percent_hours'] = 0;
                } else {
                    $value['percent_hours'] = round(($value['total_hours'] * 60 * 100) / $max);
                }
            });

            usort($contents, function ($a, $b) {
                return ($a['time'] > $b['time']) ? -1 : 1;
            });
        }

        $this->share();

        return view('web.pages.stats.index')->with('facts', $facts)
            ->with('stats', [
                [
                    'title' => _('Activities'),
                    'filter' => 'activity',
                    'rows' => $activities
                ],
                [
                    'title' => _('Tags'),
                    'filter' => 'tag',
                    'rows' => $tags
                ],
                [
                    'title' => _('Users'),
                    'filter' => 'user',
                    'rows' => $users
                ],
            ])
            ->with('filters',$filters)
            ->with('clients', $this->clientsRepo->getClients());

    }


    public function calendar()
    {
        $first = Input::get('first');
        $last = Input::get('last');

        if (empty($first)) {
            $first = date('d/m/Y', strtotime('-1 month'));
        }

        if (empty($last)) {
            $last = date('d/m/Y');
        }

        $filters = Utils::filters([
            'first' => $first,
            'last' => $last
        ]);

        /** @var Datetime $firstFilter */
        $firstFilter = $filters['first'];

        /** @var Datetime $lastFilter */
        $lastFilter = $filters['last'];

        if ((int)$firstFilter->format('N') !== 1) {
            $filters['first'] = new Datetime(date('Y-m-d', strtotime('previous monday', $firstFilter->getTimestamp())));
        }

        if ((int)$lastFilter->format('N') !== 7) {
            $filters['last'] = new Datetime(date('Y-m-d', strtotime('next sunday', $lastFilter->getTimestamp())));
        }

        $facts = Fact::orderBy('id');
        $facts = Fact::filter($facts, $filters)->get();

        $days = [];

        foreach ($facts as $fact) {
            $day = $fact->start_time->format('Y-m-d');

            if (empty($days[$day])) {
                $days[$day] = 0;
            }

            $days[$day] += $fact->total_time;
        }

        $calendar = [];

        $first = new Datetime($filters['first']->format('Y-m-d'));

        while ($first <= $filters['last']) {
            $week = $first->format('W');
            $day = $first->format('N');
            $current = $first->format('Y-m-d');

            if (empty($calendar[$week])) {
                $calendar[$week] = [];
            }

            if (empty($calendar[$week][$day])) {
                $calendar[$week][$day] = [
                    'time' => $first->getTimestamp(),
                    'hours' => 0
                ];
            }

            if (isset($days[$current])) {
                $calendar[$week][$day]['hours'] += $days[$current];
            }

            $first->modify('+1 day');
        }

        $this->share();

        return view('web.pages.stats.calendar')
            ->with('filters',$filters)
            ->with('calendar', $calendar);

    }
}