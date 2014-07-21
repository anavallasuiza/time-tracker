<?php
namespace App\Controllers;

use App\Models, App\Libs, Response, Input;

class Api extends \Controller {
    const FACTS_TIME_LIMIT = '-1 month';

    protected $user;

    protected function user()
    {
        if (empty($this->user)) {
            $this->user = Libs\Auth::user();
        }

        return $this->user;
    }

    public function getActivities()
    {
        return Response::json([
            'data' => Models\Activities::get()
        ]);
    }

    public function getFacts()
    {
        $hostname = Input::get('hostname');

        $response = $this->required(['hostname' => $hostname]);

        if ($response !== true) {
            return $response;
        }

        $facts = Models\Facts
            ::where('id_users', '=', $this->user()->id)
            ->where('hostname', '=', $hostname)
            ->where('start_time', '>=', date('Y-m-d H:i:s', strtotime(self::FACTS_TIME_LIMIT)))
            ->withTrashed();

        return Response::json([
            'data' => $facts->get()
        ]);
    }

    public function getTags()
    {
        return Response::json([
            'data' => Models\Tags::get()
        ]);
    }

    public function getFactsTags()
    {
        $hostname = Input::get('hostname');

        $response = $this->required(['hostname' => $hostname]);

        if ($response !== true) {
            return $response;
        }

        $facts = Models\Facts
            ::where('id_users', '=', $this->user()->id)
            ->where('hostname', '=', $hostname)
            ->where('start_time', '>=', date('Y-m-d H:i:s', strtotime(self::FACTS_TIME_LIMIT)))
            ->get();

        $ids = array_column($facts->toArray(), 'id');

        if (empty($ids)) {
            return Response::json([
                'data' => []
            ]);
        }

        $facts_tags = \DB::table('facts_tags')
            ->select('id', 'id_facts', 'id_tags')
            ->whereIn('id_facts', $ids)
            ->orderBy('id', 'DESC');

        return Response::json([
            'data' => $facts_tags->get()
        ]);
    }

    private function required(array $fields)
    {
        foreach ($fields as $field => $value) {
            if (empty($value)) {
                $trace = debug_backtrace()[1];

                return Response::json(array(
                    'code' =>  404,
                    'message' => sprintf(_('"%s" field is required in %s'), _($field), $trace['function'])
                ), 404);
            }
        }

        return true;
    }

    public function setTags()
    {
        $name = trim(Input::get('name'));

        $response = $this->required(['name' => $name]);

        if ($response !== true) {
            return $response;
        }

        $tag = Models\Tags::create([
            'name' => $name
        ]);

        return Response::json([
            'id' => $tag->id
        ]);
    }

    public function setActivities()
    {
        $name = trim(Input::get('name'));

        $response = $this->required([
            'name' => $name
        ]);

        if ($response !== true) {
            return $response;
        }

        $activity = Models\Activities::create([
            'name' => $name
        ]);

        return Response::json([
            'id' => $activity->id
        ]);
    }

    public function setFacts()
    {
        $start_time = trim(Input::get('start_time'));
        $end_time = trim(Input::get('end_time'));
        $remote_id = (int)Input::get('remote_id');
        $hostname = trim(Input::get('hostname'));
        $id_activities = (int)Input::get('id_activities');

        $response = $this->required([
            'start_time' => $start_time,
            'end_time' => $end_time,
            'hostname' => $hostname,
            'remote_id' => $remote_id,
            'id_activities' => $id_activities
        ]);

        if ($response !== true) {
            return $response;
        }

        if (!($start_time = Libs\Utils::checkDate($start_time, 'Y-m-d H:i:s'))) {
            return Response::json(array(
                'code' =>  404,
                'message' => sprintf(_('"%s" has not a valid date time format (Y-m-d H:i:s)'), 'start_time')
            ), 404);
        }

        if (!($end_time = Libs\Utils::checkDate($end_time, 'Y-m-d H:i:s'))) {
            return Response::json(array(
                'code' =>  404,
                'message' => sprintf(_('"%s" has not a valid date time format (Y-m-d H:i:s)'), 'end_time')
            ), 404);
        }

        $total = (int)round(($end_time->getTimestamp() - $start_time->getTimestamp()) / 60);

        if ($total < 0) {
            return Response::json(array(
                'code' =>  404,
                'message' => _('"start_time" date is older than "end_time" date')
            ), 404);
        }

        if ($total === 0) {
            return Response::json([
                'id' => 0
            ]);
        }

        $overwrite = Models\Facts::where('id_users', '=', $this->user()->id)
            ->where('start_time', '<', $end_time)
            ->where('end_time', '>', $start_time)
            ->first();

        if ($overwrite) {
            return Response::json([
                'id' => 0
            ]);
        }

        $fact = Models\Facts::create([
            'start_time' => $start_time,
            'end_time' => $end_time,
            'total_time' => $total,
            'description' => trim(Input::get('description')),
            'hostname' => $hostname,
            'remote_id' => $remote_id,
            'id_activities' => $id_activities,
            'id_users' => $this->user()->id
        ]);

        return Response::json([
            'id' => $fact->id
        ]);
    }

    public function deleteFacts()
    {
        $remote_id = (int)Input::get('remote_id');
        $hostname = trim(Input::get('hostname'));

        foreach (['remote_id', 'hostname'] as $field) {
            if (empty($$field)) {
                return Response::json(array(
                    'code' =>  404,
                    'message' => sprintf(_('"%s" field is required'), $field)
                ), 404);
            }
        }

        Models\Facts::where([
            'hostname' => $hostname,
            'remote_id' => $remote_id,
            'id_users' => $this->user()->id
        ])->delete();

        return Response::json([
            'success' => true
        ]);
    }

    public function setFactsTags()
    {
        $id_facts = (int)Input::get('id_facts');
        $id_tags = (int)Input::get('id_tags');

        foreach (['id_facts', 'id_tags'] as $field) {
            if (empty($$field)) {
                return Response::json(array(
                    'code' =>  404,
                    'message' => sprintf(_('"%s" field is required'), $field)
                ), 404);
            }
        }

        $id = \DB::table('facts_tags')->insertGetId([
            'id_facts' => $id_facts,
            'id_tags' => $id_tags
        ]);

        return Response::json([
            'id' => $id
        ]);
    }
}
