<?php
namespace App\Controllers\Forms;

use FormManager\Form;
use FormManager\Inputs\Input;

class User {
    public function login ()
    {
        $form = new Form;

        $form->add([
            'user' => Input::text()->required()->attr([
                'placeholder' => _('Your user')
            ]),
            'password' => Input::password()->required()->attr([
                'pattern' => '.{6,}',
                'placeholder' => _('Password')
            ])
        ]);

        foreach ($form as $input) {
            $input->class('form-control');
        }

        return $form;
    }
}
