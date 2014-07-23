<?php
namespace App\Controllers;

use App\Models, App\Libs, View, Redirect, Input, Session;

class Home extends Base {
    public function login()
    {
        if ($this->user) {
            return Redirect::to('/');
        }

        if (\Config::get('auth')['method'] !== 'html') {
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
        if (\Config::get('auth')['method'] === 'basic') {
            return View::make('base')->nest('body', 'error401');
        }

        return \Response::make(View::make('base')->nest('body', 'error401'), 401);
    }

    public function error404()
    {
        return \Response::make(View::make('base')->nest('body', 'error404'), 404);
    }

    public function index()
    {
        if (is_object($action = $this->action('add', (new Forms\Fact)->add()))) {
            return $action;
        }

        if (is_object($action = $this->action('edit', (new Forms\Fact)->edit()))) {
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
            return $this->csvDownload($facts->get());
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
                    'time' => $fact->total_time
                ];
            }

            foreach ($fact->tags as $tag) {
                if (array_key_exists($tag->id, $tags)) {
                    $tags[$tag->id]['time'] += $fact->total_time;
                } else {
                    $tags[$tag->id] = [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'time' => $fact->total_time
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
                    'time' => $fact->total_time
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
        $date_format = $this->user->admin ? 'd/m/Y H:i' : 'd/m/Y';

        $output = '"'._('User').'","'._('Activity').'","'._('Description').'","'._('Tags').'","'._('Start time').'","'._('End time').'","'._('Total time').'"';

        foreach ($facts as $fact) {
            $output .= "\n".'"'.$fact->users->name.'"'
                .',"'.str_replace('"', "'", $fact->activities->name).'"'
                .',"'.str_replace('"', "'", $fact->description).'"'
                .',"'.str_replace('"', "'", implode(', ', array_column(json_decode(json_encode($fact->tags), true), 'name'))).'"'
                .',"'.$fact->start_time->format($date_format).'"'
                .',"'.$fact->end_time->format($date_format).'"'
                .',"'.$fact->start_time->diff($fact->end_time)->format('%H:%I').'"';
        }

        return \Response::make($output, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="'._('time-tracking.csv').'"'
        ]);
    }

    public function dumpSQL()
    {
        if (empty($this->user->admin)) {
            return Redirect::to('/401');
        }

        $config = \Config::get('database.connections.mysql');
        $file = storage_path().'/work/dump.sql';

        try {
            (new \Ifsnop\Mysqldump\Mysqldump(
                $config['database'],
                $config['username'],
                $config['password'],
                'localhost',
                'mysql',
                ['add-drop-table' => true]
            ))->start($file);
        } catch (\Exception $e) {
            return Redirect::to('/')->with('flash-message', [
                'message' => (_('SQL Dump could not be created').': '.$e->getMessage()),
                'status' => 'danger'
            ]);
        }

        $output = file_get_contents($file);

        unlink($file);

        preg_match_all('/INSERT INTO `users` VALUES ([^;]+);/i', $output, $users);
        preg_match_all('/\([^\)]+\)/', $users[1][0], $users);

        foreach ($users[0] as $user) {
            $clean = str_getcsv(str_replace(['(', ')'], '', $user), ',', "'");

            $clean[2] = uniqid();
            $clean[3] = sha1(microtime());

            $output = str_replace($user, "('".implode("','", $clean)."')", $output);
        }

        $output = "SET FOREIGN_KEY_CHECKS=0;\n\n"
            .$output."\n\nSET FOREIGN_KEY_CHECKS=1;";

        return \Response::make($output, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="'._('time-tracking.sql').'"'
        ]);
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
