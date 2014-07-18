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

        return View::make('base')->nest('body', 'index', [
            'facts' => $facts,
            'total_time' => Libs\Utils::sumHours($facts),
            'users' => Models\Users::orderBy('name', 'ASC')->get(),
            'activities' => Models\Activities::orderBy('name', 'ASC')->get(),
            'tags' => Models\Tags::orderBy('name', 'ASC')->get(),
            'rows' => $rows,
            'sort' => $filters['sort'],
            'filter' => $filters
        ]);
	}

    public function csvDownload($facts)
    {
        $output = '"'._('User').'","'._('Activity').'","'._('Description').'","'._('Tags').'","'._('Start time').'","'._('End time').'","'._('Total time').'"';

        foreach ($facts as $fact) {
            $output .= "\n".'"'.$fact->users->name.'"'
                .',"'.str_replace('"', "'", $fact->activities->name).'"'
                .',"'.str_replace('"', "'", $fact->description).'"'
                .',"'.str_replace('"', "'", implode(', ', array_column(json_decode(json_encode($fact->tags), true), 'name'))).'"'
                .',"'.$fact->start_time->format('d/m/Y H:i').'"'
                .',"'.$fact->end_time->format('d/m/Y H:i').'"'
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

            $clean[2] = '';
            $clean[3] = sha1(microtime());

            $output = str_replace($user, "('".implode("','", $clean)."')", $output);
        }

        $output = "SET FOREIGN_KEY_CHECKS=0;\n".$output."\nSET FOREIGN_KEY_CHECKS=1;";

        return \Response::make($output, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="'._('time-tracking.sql').'"'
        ]);
    }

    public function gitUpdate()
    {
        $Shell = new Libs\Shell();

        if (!$Shell->exists('git')) {
            Session::flash('flash-message', [
                'message' => _('GIT command not exists'),
                'status' => 'danger'
            ]);

            return View::make('base')->nest('body', 'git-update', [
                'response' => _('GIT command not exists')
            ]);
        }

        $Shell->exec('git pull -u origin master');

        $log = $Shell->getLog();
        $log = end($log);

        if ($log['success']) {
            Session::flash('flash-message', [
                'status' => 'success',
                'message' => _('Environment updated successfully')
            ]);
        } else {
            Session::flash('flash-message', [
                'status' => 'danger',
                'message' => _('Error updating environment from git')
            ]);
        }

        return View::make('base')->nest('body', 'git-update', [
            'response' => ($log['success'] ? $log['response'] : $log['error'])
        ]);
    }
}
