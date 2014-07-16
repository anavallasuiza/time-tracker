<?php
namespace App\Controllers;

use \App\Models, \Response, \Input;

class Api extends ApiBase {
    public function getActivities()
    {
        return Response::json([
            'data' => Models\Activities::orderBy('id', 'DESC')->get()
        ]);
    }

    public function getCategories()
    {
        return Response::json([
            'data' => Models\Categories::orderBy('id', 'DESC')->get()
        ]);
    }

    public function getFacts()
    {
        $facts = Models\Facts::orderBy('id', 'DESC');

        if (Input::get('tags')) {
            $facts->with(['tags']);
        }

        return Response::json([
            'data' => $facts->get()
        ]);
    }

    public function getTags()
    {
        $tags = Models\Tags::orderBy('name', 'ASC');

        if (Input::get('facts')) {
            $tags->with(['facts']);
        }

        return Response::json([
            'data' => $tags->get()
        ]);
    }

    public function getFactsTags()
    {
        $facts_tags = \DB::table('facts_tags')
            ->select('id', 'id_facts', 'id_tags')
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
        $id_activities = (int)Input::get('id_activities');

        foreach (['start_time', 'end_time', 'remote_id', 'id_activities'] as $field) {
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
            'remote_id' => $remote_id,
            'id_activities' => $id_activities,
            'id_users' => $this->user()->id
        ]);

        return Response::json([
            'id' => $fact->id
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
