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
        if (is_object($action = $this->action('factAdd', (new Forms\Fact)->add()))) {
            return $action;
        }

        if (is_object($action = $this->action('factEdit', (new Forms\Fact)->edit()))) {
            return $action;
        }

        list($facts, $filters) = Models\Facts::filter([
            'user' => Input::get('user'),
            'activity' => Input::get('activity'),
            'tag' => Input::get('tag'),
            'first' => Input::get('first'),
            'last' => Input::get('last'),
            'description' => Input::get('description'),
            'sort' => Input::get('sort')
        ]);

        if (Input::get('export') === 'csv') {
            return \App\Actions\Home::csvDownload($facts->get());
        }

        $rows = in_array((int)Input::get('rows'), [-1, 20, 50, 100], true) ? (int)Input::get('rows') : 20;

        if ($rows === -1) {
            $facts = $facts->get();
        } else {
            $facts = $facts->paginate($rows);
        }

        View::share([
            'users' => ($this->user->admin ? Models\Users::orderBy('name', 'ASC')->get() : []),
            'activities' => Models\Activities::orderBy('name', 'ASC')->get(),
            'tags' => Models\Tags::orderBy('name', 'ASC')->get()
        ]);

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
        $first = Input::get('first');

        if (empty($first)) {
            $first = date('d/m/Y', strtotime('-1 month'));
        }

        list($facts, $filters) = Models\Facts::filter([
            'user' => Input::get('user'),
            'activity' => Input::get('activity'),
            'tag' => Input::get('tag'),
            'tag_unique' => true,
            'first' => $first,
            'last' => Input::get('last'),
            'description' => Input::get('description'),
            'sort' => Input::get('sort')
        ]);

        $facts = $facts->get();

        $activities = $tags = $users = [];

        $add_users = $this->user->admin && ($filters['activity'] || $filters['tag']);

        foreach ($facts as $fact) {
            if (array_key_exists($fact->activities->id, $activities)) {
                $activities[$fact->activities->id]['time'] += $fact->total_time;
            } else {
                $activities[$fact->activities->id] = [
                    'id' => $fact->activities->id,
                    'name' => $fact->activities->name,
                    'time' => $fact->total_time,
                    'selected' => ($filters['activity'] === $fact->activities->id)
                ];
            }

            foreach ($fact->tags as $tag) {
                if (array_key_exists($tag->id, $tags)) {
                    $tags[$tag->id]['time'] += $fact->total_time;
                } else {
                    $tags[$tag->id] = [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'time' => $fact->total_time,
                        'selected' => ($filters['tag'] === $tag->id)
                    ];
                }
            }

            if (empty($add_users)) {
                continue;
            }

            if (array_key_exists($fact->users->id, $users)) {
                $users[$fact->users->id]['time'] += $fact->total_time;
            } else {
                $users[$fact->users->id] = [
                    'id' => $fact->users->id,
                    'name' => $fact->users->name,
                    'time' => $fact->total_time,
                    'selected' => ($filters['user'] === $fact->users->id)
                ];
            }
        }

        foreach (['activities', 'tags', 'users'] as $stats) {
            $contents = &$$stats;

            if (empty($contents)) {
                continue;
            }

            $max = array_sum(array_column($contents, 'time'));

            array_walk($contents, function (&$value) use ($max) {
                $value['percent'] = round(($value['time'] * 100) / $max);
            });

            usort($contents, function ($a, $b) {
                return ($a['time'] > $b['time']) ? -1 : 1;
            });
        }

        View::share([
            'users' => ($this->user->admin ? Models\Users::orderBy('name', 'ASC')->get() : []),
            'activities' => Models\Activities::orderBy('name', 'ASC')->get(),
            'tags' => Models\Tags::orderBy('name', 'ASC')->get()
        ]);

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

    public function edit()
    {
        return View::make('base')->nest('body', 'edit', [
            'activities' => Models\Activities::orderBy('name', 'ASC')->get(),
            'tags' => Models\Tags::orderBy('name', 'ASC')->get()
        ]);
    }

    public function activity($id)
    {
        $form = (new Forms\Activity)->edit();

        if (is_object($action = $this->action(__FUNCTION__, $form))) {
            return $action;
        }

        $activity = Models\Activities::where('id', '=', (int)$id)->firstOrFail();

        $form->load($activity);

        $tags = Models\Tags::orderBy('name', 'ASC')->with(['estimations' => function ($query) use ($activity) {
            $query->where('id_activities', '=', $activity->id);
        }])->get();

        return View::make('base')->nest('body', 'activity', [
            'form' => $form,
            'activity' => $activity,
            'tags' => $tags
        ]);
    }

    public function tag($id)
    {
        $form = (new Forms\Tag)->edit();

        if (is_object($action = $this->action(__FUNCTION__, $form))) {
            return $action;
        }

        $tag = Models\Tags::where('id', '=', (int)$id)->firstOrFail();

        $form->load($tag);

        return View::make('base')->nest('body', 'tag', [
            'form' => $form,
            'tag' => $tag
        ]);
    }

    public function sync()
    {
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
        return $this->action(__FUNCTION__);
    }

    public function sqlDownload()
    {
        if (empty($this->user->admin)) {
            return Redirect::to('/401');
        }

        return $this->action(__FUNCTION__);
    }

    public function gitUpdate()
    {
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
}
