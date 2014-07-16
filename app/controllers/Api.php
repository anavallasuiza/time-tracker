<?php
namespace App\Controllers;

use \App\Models, \Response, \Input;

class Api extends ApiBase {
    public function getActivities()
    {
        return Response::json([
            'data' => Models\Activities::get()
        ]);
    }

    public function getCategories()
    {
        return Response::json([
            'data' => Models\Categories::get()
        ]);
    }

    public function getFacts()
    {
        $hostname = Input::get('hostname');

        if (empty($hostname)) {
            throw new \Exception(_('"hostname" parameter is required'));
        }

        $facts = Models\Facts
            ::where('id_users', '=', $this->user()->id)
            ->where('hostname', '=', $hostname);

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

        if (empty($hostname)) {
            throw new \Exception(_('"hostname" parameter is required'));
        }

        $facts = Models\Facts
            ::where('id_users', '=', $this->user()->id)
            ->where('hostname', '=', $hostname)
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

    public function setCategories()
    {
        $name = trim(Input::get('name'));

        if (empty($name)) {
            return Response::json(array(
                'code' =>  404,
                'message' => sprintf(_('"%s" field is required'), 'name')
            ), 404);
        }

        $category = Models\Categories::create([
            'name' => $name
        ]);

        return Response::json([
            'id' => $category->id
        ]);
    }

    public function setTags()
    {
        $name = trim(Input::get('name'));

        if (empty($name)) {
            return Response::json(array(
                'code' =>  404,
                'message' => sprintf(_('"%s" field is required'), 'name')
            ), 404);
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
        $id_categories = (int)Input::get('id_categories');

        foreach (['name', 'id_categories'] as $field) {
            if (empty($$field)) {
                return Response::json(array(
                    'code' =>  404,
                    'message' => sprintf(_('"%s" field is required'), $field)
                ), 404);
            }
        }

        $activity = Models\Activities::create([
            'name' => $name,
            'id_categories' => $id_categories
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

        foreach (['start_time', 'end_time', 'hostname', 'remote_id', 'id_activities'] as $field) {
            if (empty($$field)) {
                return Response::json(array(
                    'code' =>  404,
                    'message' => sprintf(_('"%s" field is required'), $field)
                ), 404);
            }
        }

        $fact = Models\Facts::create([
            'start_time' => $start_time,
            'end_time' => $end_time,
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
