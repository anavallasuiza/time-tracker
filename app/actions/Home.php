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

        $start = trim(Input::get('start'));
        $end = trim(Input::get('end'));

        if (!($start = Libs\Utils::checkDate($start, 'd/m/Y H:i'))) {
            throw new \Exception(sprintf(_('"%s" has not a valid date time format (d-m-Y H:i)'), _('start_time')));
        }

        if (!($end = Libs\Utils::checkDate($end, 'd/m/Y H:i'))) {
            throw new \Exception(sprintf(_('"%s" has not a valid date time format (d-m-Y H:i)'), _('end_time')));
        }

        $total = (int)round(($end->getTimestamp() - $start->getTimestamp()) / 60);

        if ($total <= 0) {
            throw new \Exception(_('"start_time" date is older than "end_time" date'));
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

        $start = trim(Input::get('start'));
        $end = trim(Input::get('end'));

        if (!($start = Libs\Utils::checkDate($start, 'd/m/Y H:i'))) {
            throw new \Exception(sprintf(_('"%s" has not a valid date time format (d-m-Y H:i)'), _('start_time')));
        }

        if (!($end = Libs\Utils::checkDate($end, 'd/m/Y H:i'))) {
            throw new \Exception(sprintf(_('"%s" has not a valid date time format (d-m-Y H:i)'), _('end_time')));
        }

        $total = (int)round(($end->getTimestamp() - $start->getTimestamp()) / 60);

        if ($total <= 0) {
            throw new \Exception(_('"start_time" date is older than "end_time" date'));
        }

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
}