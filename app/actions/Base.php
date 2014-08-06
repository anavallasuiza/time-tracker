<?php
namespace App\Actions;

use App\Libs, App\Controllers;

class Base {
    public function __construct()
    {
        $this->user = \Auth::user();
    }

    protected function check($function, \FormManager\Form $form = null, $admin = false)
    {
        if ($admin && empty($this->user->admin)) {
            return Redirect::to('/404');
        }

        $post = \Input::all();

        if (empty($post['action']) || ($post['action'] !== $function)) {
            return null;
        }

        if (Libs\Utils::isBot($post) || empty($post['_token'])) {
            throw new \ErrorException(_('Not allowed'));
        }

        if ($form === null) {
            return $post;
        }

        $form->load($post);

        if ($form->isValid() !== true) {
            foreach ($form as $input) {
                if ($input->error()) {
                    throw new \ErrorException($input->attr('name').': '.$input->error());
                }
            }

            throw new \ErrorException(_('You must fill form with all required fields'));
        }

        $data = $form->val();

        unset($data['action'], $data['_token']);

        return $data;
    }
}