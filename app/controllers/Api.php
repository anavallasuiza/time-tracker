<?php
namespace App\Controllers;

use \App\Models, \Response, \Input;

class Api extends ApiBase {
    public function getActivities()
    {
        return Response::json([
            'data' => Models\Activities::orderBy('name', 'ASC')->get()
        ]);
    }

    public function getCategories()
    {
        return Response::json([
            'data' => Models\Categories::orderBy('name', 'ASC')->get()
        ]);
    }

    public function getFacts()
    {
        return Response::json([
            'data' => Models\Facts::orderBy('id', 'DESC')->get()
        ]);
    }

    public function getTags()
    {
        return Response::json([
            'data' => Models\Tags::orderBy('name', 'ASC')->get()
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
            'remote_id' => $remote_id,
            'id_activities' => $id_activities,
            'id_users' => $this->user()->id
        ]);

        return Response::json([
            'id' => $fact->id
        ]);
    }
}
