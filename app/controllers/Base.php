<?php
namespace App\Controllers;

use View;
use App\Libs, App\Models;

class Base extends \Controller {
    public function __construct()
    {
        \View::share('user', $this->user = Libs\Auth::user());
    }

    public function action($action, \FormManager\Form $form = null, array $params = [])
    {
        if (!\Request::isMethod('post')) {
            return null;
        }

        try {
            $class = explode('\\', get_class($this));
            return \App::make('\\App\\Actions\\'.end($class))->$action($form, $params);
        } catch (\Exception $e) {
            $message = '';

            if (\App::environment('local')) {
                $message = '['.$e->getFile().' - '.$e->getLine().'] ';
            }

            $response = Libs\Utils::setMessage([
                'message' => ($message.$e->getMessage()),
                'status' => 'danger'
            ], 401);

            return is_object($response) ? $response : false;
        }
    }

    protected function share()
    {
        if ($this->user->admin) {
            $users = Models\Users::orderBy('name', 'ASC')->get();
        } else {
            $users = Models\Users::where('id', '=', $this->user->id)->get();
        }

        $activities = Models\Activities::where('archived', '=', 0)
            ->orderBy('name', 'ASC')
            ->get();

        View::share([
            'users' => $users,
            'activities' => $activities,
            'tags' => Models\Tags::orderBy('name', 'ASC')->get()
        ]);
    }
}