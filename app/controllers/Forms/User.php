<?php
namespace App\Controllers\Forms;

use FormManager\Inputs\Input;
use FormManager\Fields\Field;

class User
{
    public function login ()
    {
        return (new Base())->add([
            'user' => Field::text()->required()->attr([
                'placeholder' => _('Your user')
            ]),
            'password' => Field::password()->required()->attr([
                'pattern' => '.{6,}',
                'placeholder' => _('Password')
            ])
        ])->wrapperInputs();
    }

    public function edit ()
    {
        return (new Base())->add([
            'id' => Input::hidden(),
            'name' => Field::text()->required()->attr([
                'placeholder' => _('Name')
            ]),
            'user' => Field::text()->required()->attr([
                'placeholder' => _('User')
            ]),
            'email' => Field::email()->attr([
                'placeholder' => _('Email')
            ]),
            'password' => Field::password()->attr([
                'placeholder' => _('Password')
            ]),
            'password_repeat' => Field::password()->attr([
                'placeholder' => _('Password Repeat')
            ]),
            'api_key' => Field::text()->attr([
                'placeholder' => _('API Key')
            ]),
            'store_hours' => Field::checkbox()->attr([
                'class' => 'bootstrap-switch',
                'placeholder' => _('Store Hours'),
                'value' => 1
            ]),
            'enabled' => Field::checkbox()->attr([
                'class' => 'bootstrap-switch',
                'placeholder' => _('Enabled'),
                'value' => 1
            ])
        ])->wrapperInputs();
    }
}
