<?php
namespace App\Controllers;

use App\Libs;

class Base extends \Controller {
    public function __construct()
    {
        $this->user = \Auth::user();
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

            Libs\Utils::setMessage([
                'message' => ($message.$e->getMessage()),
                'status' => 'danger'
            ]);

            return false;
        }
    }
}