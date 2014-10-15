<?php
namespace App\Controllers;

use Config, Input, Redirect, Response, Session, View;
use App\Models, App\Libs;

class Home extends Base {
    public function login()
    {
        if ($this->user) {
            return Redirect::to('/');
        }

        if (Config::get('auth')['method'] !== 'html') {
            return Redirect::to('/401');
        }

        $form = (new Forms\User)->login();

        if (is_object($action = $this->action(__FUNCTION__, $form))) {
            return $action;
        }

        return View::make('base')->nest('body', 'login', [
            'form' => $form
        ]);
    }

    public function error401()
    {
        if (Config::get('auth')['method'] === 'basic') {
            return View::make('base')->nest('body', 'error401');
        }

        return Response::make(View::make('base')->nest('body', 'error401'), 401);
    }

    public function error404()
    {
        return Response::make(View::make('base')->nest('body', 'error404'), 404);
    }

    public function index()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        if (is_object($action = $this->action('factAdd', (new Forms\Fact)->add()))) {
            return $action;
        }

        if (is_object($action = $this->action('factEdit', (new Forms\Fact)->edit()))) {
            return $action;
        }

        $filters = Libs\Utils::filters();
        $facts = Models\Facts::with(['activities'])
            ->with(['tags'])
            ->with(['users']);

        $facts = Models\Facts::filter($facts, $filters);

        if (Input::get('export') === 'csv') {
            return \App\Actions\Home::csvDownload($facts->get());
        }

        $rows = in_array((int)Input::get('rows'), [-1, 20, 50, 100], true) ? (int)Input::get('rows') : 20;

        if ($rows === -1) {
            $facts = $facts->get();
        } else {
            $facts = $facts->paginate($rows);
        }

        $this->share();

        return View::make('base')->nest('body', 'index', [
            'facts' => $facts,
            'total_time' => Libs\Utils::sumHours($facts),
            'rows' => $rows,
            'sort' => $filters['sort'],
            'filters' => $filters
        ]);
    }

    public function stats()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        $first = Input::get('first');

        if (empty($first)) {
            $first = date('d/m/Y', strtotime('-1 month'));
        }

        $filters = Libs\Utils::filters([
            'first' => $first
        ]);

        $facts = Models\Facts::select('id_activities')->groupBy('id_activities');
        $facts = Models\Facts::filter($facts, $filters)->get();

        $ids = Libs\Utils::objectColumn($facts, 'id_activities') ?: [0];

        $newFilters = $filters;

        if (empty($filters['times'])) {
            $newFilters = $filters;
            $newFilters['first'] = false;
        }

        $facts = Models\Facts::whereIn('id_activities', $ids);
        $facts = Models\Facts::filter($facts, $newFilters)
            ->with(['activities'])
            ->with(['users']);

        if ($filters['tag']) {
            $facts->with(['tags' => function ($query) use ($filters) {
                $query->where('tags.id', '=', $filters['tag']);
            }]);
        } else {
            $facts->with(['tags']);
        }

        $facts = $facts->get();

        $tmp = Models\Estimations::whereIn('id_activities', $ids);

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

            if (empty($this->user->admin) && ($fact->users->id !== $this->user->id)) {
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

        return View::make('base')->nest('body', 'stats', [
            'filters' => $filters,
            'stats' => [
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
            ]
        ]);
    }

    public function statsCalendar()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        $first = Input::get('first');
        $last = Input::get('last');

        if (empty($first)) {
            $first = date('d/m/Y', strtotime('-1 month'));
        }

        if (empty($last)) {
            $last = date('d/m/Y');
        }

        $filters = Libs\Utils::filters([
            'first' => $first,
            'last' => $last
        ]);

        if ((int)$filters['first']->format('N') !== 1) {
            $filters['first'] = new \Datetime(date('Y-m-d', strtotime('previous monday', $filters['first']->getTimestamp())));
        }

        if ((int)$filters['last']->format('N') !== 7) {
            $filters['last'] = new \Datetime(date('Y-m-d', strtotime('next sunday', $filters['last']->getTimestamp())));
        }

        $facts = Models\Facts::orderBy('id');
        $facts = Models\Facts::filter($facts, $filters)->get();

        $days = [];

        foreach ($facts as $fact) {
            $day = $fact->start_time->format('Y-m-d');

            if (empty($days[$day])) {
                $days[$day] = 0;
            }

            $days[$day] += $fact->total_time;
        }

        $calendar = [];

        $first = new \Datetime($filters['first']->format('Y-m-d'));

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

        return View::make('base')->nest('body', 'stats-calendar', [
            'filters' => $filters,
            'calendar' => $calendar
        ]);
    }

    public function edit()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        if ($this->user->admin) {
            $users = Models\Users::orderBy('name', 'ASC')->get();
        } else {
            $users = [];
        }

        return View::make('base')->nest('body', 'edit', [
            'activities' => Models\Activities::orderBy('name', 'ASC')->get(),
            'tags' => Models\Tags::orderBy('name', 'ASC')->get(),
            'users' => $users
        ]);
    }

    public function activityAdd()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        $form = (new Forms\Activity)->edit();

        if (is_object($action = $this->action(__FUNCTION__, $form))) {
            return $action;
        }

        $activity = new \stdClass();
        $activity->name = _('New');
        $activity->total_hours = 0;

        $tags = Models\Tags::orderBy('name', 'ASC')->get();

        foreach ($tags as $tag) {
            $tag->estimations = [];
        }

        return View::make('base')->nest('body', 'activity', [
            'form' => $form,
            'activity' => $activity,
            'tags' => $tags
        ]);
    }

    public function activityEdit($id)
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        $form = (new Forms\Activity)->edit();

        if (is_object($action = $this->action(__FUNCTION__, $form))) {
            return $action;
        }

        $activity = Models\Activities::where('id', '=', (int)$id)->firstOrFail();

        if ($action !== false) {
            $form->load($activity);
        }

        $tags = Models\Tags::orderBy('name', 'ASC')->with(['estimations' => function ($query) use ($activity) {
            $query->where('id_activities', '=', $activity->id);
        }])->get();

        return View::make('base')->nest('body', 'activity', [
            'form' => $form,
            'activity' => $activity,
            'tags' => $tags
        ]);
    }

    public function tagAdd()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        $form = (new Forms\Tag)->edit();

        if (is_object($action = $this->action(__FUNCTION__, $form))) {
            return $action;
        }

        $tag = new \stdClass();
        $tag->name = _('New');

        return View::make('base')->nest('body', 'tag', [
            'form' => $form,
            'tag' => $tag
        ]);
    }

    public function tagEdit($id)
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        $form = (new Forms\Tag)->edit();

        if (is_object($action = $this->action(__FUNCTION__, $form))) {
            return $action;
        }

        $tag = Models\Tags::where('id', '=', (int)$id)->firstOrFail();

        if ($action !== false) {
            $form->load($tag);
        }

        return View::make('base')->nest('body', 'tag', [
            'form' => $form,
            'tag' => $tag
        ]);
    }

    public function userAdd()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        if (empty($this->user->admin)) {
            return Redirect::to('/401');
        }

        $form = (new Forms\User)->edit();

        if (is_object($action = $this->action(__FUNCTION__, $form))) {
            return $action;
        }

        $user = new \stdClass();
        $user->name = _('New');

        $form['enabled']->attr('checked', 'checked');
        $form['api_key']->val(hash('sha256', uniqid()));

        return View::make('base')->nest('body', 'user', [
            'form' => $form,
            'user' => $user
        ]);
    }

    public function userEdit($id)
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        if (empty($this->user->admin) && ($this->user->id !== $id)) {
            return Redirect::to('/401');
        }

        $form = (new Forms\User)->edit();

        if (is_object($action = $this->action(__FUNCTION__, $form))) {
            return $action;
        }

        $user = Models\Users::where('id', '=', (int)$id)->firstOrFail();

        unset($user->password);

        if ($action !== false) {
            $form->load($user);
        }

        if ($this->user->id === $id) {
            unset($form['enabled']);
        }

        return View::make('base')->nest('body', 'user', [
            'form' => $form,
            'user' => $user
        ]);
    }

    public function sync()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        if (is_object($action = $this->action(__FUNCTION__))) {
            return $action;
        }

        return View::make('base')->nest('body', 'sync', [
            'response' => $action,
            'action' => Input::get('action')
        ]);
    }

    public function factTr($id)
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        $fact = Models\Facts::where('id', '=', (int)$id);

        if (empty($this->user->admin)) {
            $fact->where('id_users', '=', $this->user->id);
        }

        return View::make('sub-fact-tr')->with([
            'fact' => $fact->firstOrFail()
        ]);
    }

    public function csvDownload($facts)
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        return $this->action(__FUNCTION__);
    }

    public function sqlDownload()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        if (empty($this->user->admin)) {
            return Redirect::to('/401');
        }

        return $this->action(__FUNCTION__);
    }

    public function gitUpdate()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        if (empty($this->user->admin)) {
            return Redirect::to('/401');
        }

        if (is_object($action = $this->action(__FUNCTION__))) {
            return $action;
        }

        return View::make('base')->nest('body', 'git-update', [
            'action' => Input::get('action'),
            'response' => $action
        ]);
    }

    public function toolsDuplicates()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        if (empty($this->user->admin)) {
            return Redirect::to('/401');
        }

        if (is_object($action = $this->action(__FUNCTION__))) {
            return $action;
        }

        $facts = Models\Facts::where('remote_id', '>', 0)->with(['users'])->get();
        $duplicates = [];

        foreach ($facts as $fact) {
            $md5 = md5($fact->id_users.$fact->id_activities.$fact->remote_id.$fact->hostname.$fact->total_time);

            if (!array_key_exists($md5, $duplicates)) {
                $duplicates[$md5] = [];
            }

            $duplicates[$md5][] = $fact;
        }

        foreach ($duplicates as &$duplicate) {
            if (count($duplicate) === 1) {
                $duplicate = null;
                continue;
            }

            $first = array_shift($duplicate);
            $first->checked = false;

            foreach ($duplicate as &$row) {
                $row->checked = true;
            }

            array_unshift($duplicate, $first);
        }

        return View::make('base')->nest('body', 'tools-duplicates', [
            'facts' => array_values(array_filter($duplicates)),
            'action' => Input::get('action'),
            'response' => $action
        ]);
    }
}
