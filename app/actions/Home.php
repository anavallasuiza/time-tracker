<?php
namespace App\Actions;

use Exception;
use Config, DB, Input, Redirect, Response, Session;
use App\Libs, App\Models;

class Home extends Base {
    public function login($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        $response = Libs\Auth::login();

        if ($response !== true) {
            return $response;
        }

        return Redirect::to('/');
    }

    public function factAdd($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        list($start, $end, $total) = Libs\Utils::startEndTime(Input::get('start'), Input::get('end'), Input::get('time'));

        $overwrite = Models\Facts::where('id_users', '=', $this->user->id)
            ->where('start_time', '<', $start)
            ->where('end_time', '>', $end)
            ->first();

        if ($overwrite) {
            throw new Exception(_('This fact ovewrite on same time other different fact'));
        }

        try {
            $fact = Models\Facts::create([
                'start_time' => $start,
                'end_time' => $end,
                'total_time' => $total,
                'description' => trim(Input::get('description')),
                'id_activities' => (int)Input::get('activity'),
                'id_users' => $this->user->id
            ]);
        } catch (Exception $e) {
            throw new Exception(sprintf(_('Error creating fact: %s'), $e->getMessage()));
        }

        DB::table('facts_tags')
            ->where('id_facts', '=', $fact->id)
            ->delete();

        DB::table('facts_tags')->insert([
            'id_facts' => $fact->id,
            'id_tags' => (int)Input::get('tag')
        ]);

        Models\Logs::create([
            'description' => _('Created fact'),
            'date' => date('Y-m-d H:i:s'),
            'id_facts' => $fact->id,
            'id_users' => $this->user->id
        ]);

        return Response::json([
            'id' => $fact->id
        ]);
    }

    public function factEdit($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        $fact = Models\Facts::where('id', '=', Input::get('id'));

        if (empty($this->user->admin)) {
            $fact->where('id_users', '=', $this->user->id);
        }

        $fact = $fact->firstOrFail();

        list($start, $end, $total) = Libs\Utils::startEndTime(Input::get('start'), Input::get('end'), Input::get('time'));

        $fact->start_time = $start;
        $fact->end_time = $end;
        $fact->total_time = $total;
        $fact->description = Input::get('description');
        $fact->id_activities = (int)Input::get('activity');

        try {
            $fact->save();
        } catch (Exception $e) {
            throw new Exception(sprintf(_('Error updating fact: %s'), $e->getMessage()));
        }

        DB::table('facts_tags')
            ->where('id_facts', '=', $fact->id)
            ->delete();

        DB::table('facts_tags')->insert([
            'id_facts' => $fact->id,
            'id_tags' => (int)Input::get('tag')
        ]);

        Models\Logs::create([
            'description' => _('Updated fact'),
            'date' => date('Y-m-d H:i:s'),
            'id_facts' => $fact->id,
            'id_users' => $this->user->id
        ]);

        return Response::json([
            'id' => $fact->id
        ]);
    }

    public function activity($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        $activity = Models\Activities::where('id', '=', $data['id'])->firstOrFail();

        Models\Estimations::where('id_activities', '=', $activity->id)->delete();

        $tags = Models\Tags::orderBy('name', 'ASC')->get();
        $form = Input::get('tags');

        $total = 0;

        foreach ($tags as $tag) {
            if (empty((int)($hours = $form[$tag->id]))) {
                continue;
            }

            $total += $hours;

            Models\Estimations::create([
                'hours' => $hours,
                'id_activities' => $activity->id,
                'id_tags' => $tag->id
            ]);
        }

        $activity->name = $data['name'];
        $activity->total_hours = $total;
        $activity->save();

        Models\Logs::create([
            'description' => _('Updated activity'),
            'date' => date('Y-m-d H:i:s'),
            'id_activities' => $activity->id,
            'id_users' => $this->user->id
        ]);

        Session::flash('flash-message', [
            'status' => 'success',
            'message' => _('Activity updated successfully')
        ]);

        return Redirect::back();
    }

    public function tag($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        $tag = Models\Tags::where('id', '=', $data['id'])->firstOrFail();
        $tag->name = $data['name'];
        $tag->save();

        Models\Logs::create([
            'description' => _('Updated tag'),
            'date' => date('Y-m-d H:i:s'),
            'id_tags' => $tag->id,
            'id_users' => $this->user->id
        ]);

        Session::flash('flash-message', [
            'status' => 'success',
            'message' => _('Tag updated successfully')
        ]);

        return Redirect::back();
    }

    public function sync()
    {
        set_time_limit(0);

        $config = Config::get('app');

        $Shell = new Libs\Shell();

        $cmd = 'php -f "'.$config['sync_php'].'" showdb=false response=json';

        if (empty($this->user->admin)) {
            $cmd .= ' user="'.$this->user->user.'"';
        }

        $Shell->exec($cmd);

        $log = $Shell->getLog();
        $log = end($log);

        if ($log['success']) {
            Session::flash('flash-message', [
                'status' => 'success',
                'message' => _('Databases synchronized successfully')
            ]);

            $response = Libs\Utils::object2array(json_decode(trim($log['response'])));
        }

        if (!is_array($response)) {
            Session::flash('flash-message', [
                'status' => 'danger',
                'message' => _('Error synchronizing databases')
            ]);

            $response = ['error' => [
                'status' => 'danger',
                'message' => $response
            ]];
        }

        return $response;
    }

    public function gitUpdate()
    {
        set_time_limit(0);

        if (empty($this->user->admin)) {
            return Redirect::to('/401');
        }

        $Shell = new Libs\Shell();

        if (!$Shell->exists('git')) {
            Session::flash('flash-message', [
                'message' => _('GIT command not exists'),
                'status' => 'danger'
            ]);

            return _('GIT command not exists');
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

        return $log['success'] ? $log['response'] : $log['error'];
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

        return Response::make($output, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="'._('time-tracking.csv').'"'
        ]);
    }

    public function sqlDownload()
    {
        if (empty($this->user->admin)) {
            return Redirect::to('/401');
        }

        $config = Config::get('database.connections.mysql');
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
        } catch (Exception $e) {
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

        return Response::make($output, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="'._('time-tracking.sql').'"'
        ]);
    }
}