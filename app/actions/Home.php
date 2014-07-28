<?php
namespace App\Actions;

use App\Libs, App\Models, Input, Response;

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

        return \Redirect::to('/');
    }

    public function add($form)
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
            throw new \Exception(_('This fact ovewrite on same time other different fact'));
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
        } catch (\Exception $e) {
            throw new \Exception(sprintf(_('Error creating fact: %s'), $e->getMessage()));
        }

        \DB::table('facts_tags')
            ->where('id_facts', '=', $fact->id)
            ->delete();

        \DB::table('facts_tags')->insert([
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

    public function edit($form)
    {
        if (!($data = $this->check(__FUNCTION__, $form))) {
            return false;
        }

        $fact = Models\Facts::where('id', '=', \Input::get('id'));

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
        } catch (\Exception $e) {
            throw new \Exception(sprintf(_('Error updating fact: %s'), $e->getMessage()));
        }

        \DB::table('facts_tags')
            ->where('id_facts', '=', $fact->id)
            ->delete();

        \DB::table('facts_tags')->insert([
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

    public function sync()
    {
        set_time_limit(0);

        $config = \Config::get('app');

        $Shell = new Libs\Shell();

        $cmd = 'php -f "'.$config['sync_php'].'" showdb=false response=json';

        if (empty($this->user->admin)) {
            $cmd .= ' user="'.$this->user->user.'"';
        }

        $Shell->exec($cmd);

        $log = $Shell->getLog();
        $log = end($log);

        if ($log['success']) {
            \Session::flash('flash-message', [
                'status' => 'success',
                'message' => _('Databases synchronized successfully')
            ]);

            $response = Libs\Utils::object2array(json_decode(trim($log['response'])));
        }

        if (!is_array($response)) {
            \Session::flash('flash-message', [
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
            \Session::flash('flash-message', [
                'message' => _('GIT command not exists'),
                'status' => 'danger'
            ]);

            return _('GIT command not exists');
        }

        $Shell->exec('git pull -u origin master');

        $log = $Shell->getLog();
        $log = end($log);

        if ($log['success']) {
            \Session::flash('flash-message', [
                'status' => 'success',
                'message' => _('Environment updated successfully')
            ]);
        } else {
            \Session::flash('flash-message', [
                'status' => 'danger',
                'message' => _('Error updating environment from git')
            ]);
        }

        return $log['success'] ? $log['response'] : $log['error'];
    }
}