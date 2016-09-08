<?php
namespace App\Http\Controllers\Actions;

use Exception;
use Config, DB, Input, Redirect, Response, Session;
use App\Libs, App\Database\Models;

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

        $overwrite = Models\Fact::where('id_users', '=', $this->user->id)
            ->where('start_time', '<', $start)
            ->where('end_time', '>', $end)
            ->first();

        if ($overwrite) {
            throw new Exception(_('This fact ovewrite on same time other different fact'));
        }

        try {
            $fact = Models\Fact::create([
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

        Models\Log::create([
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

        $fact = Models\Fact::where('id', '=', Input::get('id'));

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

        Models\Log::create([
            'description' => _('Updated fact'),
            'date' => date('Y-m-d H:i:s'),
            'id_facts' => $fact->id,
            'id_users' => $this->user->id
        ]);

        return Response::json([
            'id' => $fact->id
        ]);
    }

    public function activityAdd($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        $exists = Models\Activity::where('name', '=', $data['name'])->first();

        if ($exists) {
            Session::flash('flash-message', [
                'status' => 'danger',
                'message' => sprintf(_('Activity %s already exists'), $data['name'])
            ]);

            return false;
        }

        $activity = Models\Activity::create([
            'name' => $data['name'],
            'archived' => is_null($data['archived']) ? false : $data['archived'],
            'id_clients' => isset($data['id_clients']) && $data['id_clients']!=-1?$data['id_clients']:null
        ]);

        $tags = Models\Tag::orderBy('name', 'ASC')->get();
        $form = Input::get('tags');

        $total = 0;

        foreach ($tags as $tag) {
            if (empty((int)($hours = $form[$tag->id]))) {
                continue;
            }

            $total += $hours;

            Models\Estimation::create([
                'hours' => $hours,
                'id_activities' => $activity->id,
                'id_tags' => $tag->id
            ]);
        }

        $activity->total_hours = $total;
        $activity->save();

        Models\Log::create([
            'description' => sprintf(_('Added activity %s'), $activity->name),
            'date' => date('Y-m-d H:i:s'),
            'id_activities' => $activity->id,
            'id_users' => $this->user->id
        ]);

        Session::flash('flash-message', [
            'status' => 'success',
            'message' => _('Activity created successfully')
        ]);

        return Redirect::to('/activity/'.$activity->id);
    }

    public function activityEdit($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        $activity = Models\Activity::where('id', '=', $data['id'])->firstOrFail();

        Models\Estimation::where('id_activities', '=', $activity->id)->delete();

        $tags = Models\Tag::orderBy('name', 'ASC')->get();
        $form = Input::get('tags');

        $total = 0;

        foreach ($tags as $tag) {
            if (empty((int)($hours = $form[$tag->id]))) {
                continue;
            }

            $total += $hours;

            Models\Estimation::create([
                'hours' => $hours,
                'id_activities' => $activity->id,
                'id_tags' => $tag->id
            ]);
        }

        $activity->name = $data['name'];
        $activity->archived = $data['archived'];
        $activity->total_hours = $total;

        if(isset($data['id_clients']))
        {
            $activity->id_clients = $data['id_clients']!=-1?$data['id_clients']:null;
        }
        $activity->save();

        Models\Log::create([
            'description' => sprintf(_('Updated activity %s'), $activity->name),
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

    public function tagAdd($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        $exists = Models\Tag::where('name', '=', $data['name'])->first();

        if ($exists) {
            Session::flash('flash-message', [
                'status' => 'danger',
                'message' => sprintf(_('Tag %s already exists'), $data['name'])
            ]);

            return false;
        }

        $tag = Models\Tag::create([
            'name' => $data['name']
        ]);

        Models\Log::create([
            'description' => sprintf(_('Created tag %s'), $tag->name),
            'date' => date('Y-m-d H:i:s'),
            'id_tags' => $tag->id,
            'id_users' => $this->user->id
        ]);

        Session::flash('flash-message', [
            'status' => 'success',
            'message' => _('Tag created successfully')
        ]);

        return Redirect::to('/tag/'.$tag->id);
    }

    public function tagEdit($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        $tag = Models\Tag::where('id', '=', $data['id'])->firstOrFail();
        $tag->name = $data['name'];
        $tag->save();

        Models\Log::create([
            'description' => sprintf(_('Updated tag %s'), $tag->name),
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

    public function userAdd($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        if (empty($this->user->admin)) {
            return Redirect::to('/401');
        }

        if (empty($data['password'])) {
            throw new \ErrorException(_('Password is required.'));
        }

        if ($data['password'] !== $data['password_repeat']) {
            throw new \ErrorException(_('Passwords must be equals.'));
        }

        $exists = Models\User::where('user', '=', $data['user'])->first();

        if (count($exists)) {
            throw new \ErrorException(sprintf(_('%s user can\'t be used, it\'s already registered :('), $data['user']));
        }

        $user = Models\User::create([
            'name' => $data['name'],
            'user' => $data['user'],
            'email' => $data['email'],
            'api_key' => $data['api_key'],
            'store_hours' => ($data['store_hours'] ?: 0),
            'enabled' => $data['enabled'],
            'password' => \Hash::make($data['password'])
        ]);

        Models\Log::create([
            'description' => sprintf(_('Updated user %s'), $user->name),
            'date' => date('Y-m-d H:i:s'),
            'id_users' => $this->user->id
        ]);

        Session::flash('flash-message', [
            'status' => 'success',
            'message' => _('User updated successfully')
        ]);

        return Redirect::to('/user/'.$user->id);
    }

    public function userEdit($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        if (empty($this->user->admin) && ($this->user->id !== $data['id'])) {
            return Redirect::to('/401');
        }

        if ($data['password'] !== $data['password_repeat']) {
            throw new \ErrorException(_('Passwords must be equals.'));
        }

        $user = Models\User::where('id', '=', $data['id'])->firstOrFail();

        $exists = Models\User::
            where('user', '=', $data['user'])
            ->where('id', '!=', $data['id'])
            ->first();

        if (count($exists)) {
            throw new \ErrorException(sprintf(_('%s user can\'t be used, it\'s already registered :('), $data['user']));
        }

        $user->name = $data['name'];
        $user->user = $data['user'];
        $user->email = $data['email'];
        $user->api_key = $data['api_key'];
        $user->store_hours = $data['store_hours'] ?: 0;

        if ($data['id'] !== $this->user->id) {
            $user->enabled = $data['enabled'];
        }

        if ($data['password']) {
            $user->password = \Hash::make($data['password']);
        }

        $user->save();

        Models\Log::create([
            'description' => sprintf(_('Updated user %s'), $user->name),
            'date' => date('Y-m-d H:i:s'),
            'id_users' => $this->user->id
        ]);

        Session::flash('flash-message', [
            'status' => 'success',
            'message' => _('User updated successfully')
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

        $update = Input::get('update');

        if ($update === 'repository') {
            $Shell->exec('git pull -u origin master');
        } elseif ($update === 'composer') {
            $Shell->exec('export COMPOSER_HOME="'.base_path().'"; composer update');
        } else {
            return Redirect::to('/401');
        }

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

    public function toolsDuplicates()
    {
        if (empty($this->user)) {
            return Redirect::to('/login');
        }

        if (empty($this->user->admin)) {
            return Redirect::to('/401');
        }

        $checked = Input::get('checked');

        if (empty($checked)) {
            Session::flash('flash-message', [
                'status' => 'warning',
                'message' => _('No facts selected')
            ]);

            return Redirect::back();
        }

        $facts = Models\Fact::where('remote_id', '>', 0)->get();
        $duplicates = $delete = $times = [];

        foreach ($facts as $fact) {
            $md5 = md5($fact->id_users.$fact->id_activities.$fact->remote_id.$fact->hostname.$fact->total_time);

            if (!array_key_exists($md5, $duplicates)) {
                $duplicates[$md5] = [];
            }

            $duplicates[$md5][] = $fact;
        }

        foreach ($duplicates as $duplicate) {
            if (count($duplicate) === 1) {
                continue;
            }

            foreach ($duplicate as $row) {
                if (in_array($row->id, $checked)) {
                    $delete[] = $row->id;
                }
            }
        }

        if ($delete) {
            Models\Fact::destroy($delete);
        }

        Models\Log::create([
            'description' => sprintf(_('Deleted %s duplicated facts'), count($delete)),
            'date' => date('Y-m-d H:i:s'),
            'id_users' => $this->user->id
        ]);

        Session::flash('flash-message', [
            'status' => 'success',
            'message' => _('Facts removed successfully')
        ]);

        return Redirect::back();
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