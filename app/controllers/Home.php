<?php
namespace App\Controllers;

use \App\Models, \App\Libs, \View, \Redirect, \Input, \Session;

class Home extends Base {

	public function index()
	{
        $facts = Models\Facts::orderBy('facts.end_time', 'DESC')
            ->with(['activities'])
            ->with(['users'])
            ->with(['tags']);

        if ($user = (int)Input::get('user')) {
            $facts->where('id_users', '=', $user);
        }

        if ($activity = (int)Input::get('activity')) {
            $facts->where('id_activities', '=', $activity);
        }

        if ($tag = (int)Input::get('tag')) {
            $facts->join('facts_tags', 'facts_tags.id_facts', '=', 'facts.id')
                ->where('facts_tags.id_tags', '=', $tag);
        }

        if (($first = Input::get('first')) && ($first = Libs\Utils::checkdate($first))) {
            $facts->where('end_time', '>=', $first->format('Y-m-d 00:00:00'));
        } else {
            $first = null;
        }

        if (($last = Input::get('last')) && ($last = Libs\Utils::checkdate($last))) {
            $facts->where('end_time', '<=', $last->format('Y-m-d 23:59:59'));
        } else {
            $last = null;
        }

        if ($description = Input::get('description')) {
            $facts->where('description', 'LIKE', '%'.$description.'%');
        }

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
            'filter' => [
                'user' => $user,
                'activity' => $activity,
                'tag' => $tag,
                'description' => $description,
                'first' => $first,
                'last' => $last
            ]
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
