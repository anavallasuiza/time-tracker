<?php
namespace App\Actions;

use App\Libs;

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

        return \Redirect::intended('/');
    }
}