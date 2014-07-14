<?php
namespace App\Controllers;

use \App\Models;

class ApiBase extends \Controller {
    protected $user;

    public function user()
    {
        if (empty($this->user)) {
            $this->user = Models\Users
                ::where('email', '=', \Input::get('email'))
                ->where('hash', '=', \Input::get('hash'))
                ->first();
        }

        return $this->user;
    }
}
