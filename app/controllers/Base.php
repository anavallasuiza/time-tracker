<?php
namespace App\Controllers;

use App\Libs;

class Base extends \Controller {
    public function __construct()
    {
        \View::share('user', $this->user = \Auth::user());
    }

    public function action($action, $form, array $params = [])
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
}