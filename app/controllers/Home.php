<?php
namespace App\Controllers;

use \App\Models, \View, \Input;

class Home extends Base {

	public function index()
	{
        $user = (int)Input::get('user');
        $activity = (int)Input::get('activity');

        $facts = Models\Facts::orderBy('id', 'DESC')->with(['activities'])->with(['users']);

        if ($user) {
            $facts->where('id_users', '=', $user);
        } if ($activity) {
            $facts->where('id_activities', '=', $activity);
        }

        return View::make('base')->nest('body', 'index', [
            'facts' => $facts->get(),
            'users' => Models\Users::orderBy('name', 'ASC')->get(),
            'activities' => Models\Activities::orderBy('name', 'ASC')->get(),
            'filter' => [
                'user' => $user,
                'activity' => $activity
            ]
        ]);
	}
}
