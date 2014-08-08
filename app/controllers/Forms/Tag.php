<?php
namespace App\Controllers\Forms;

use FormManager\Form;
use FormManager\Inputs\Input;

class Tag {
    public function edit ()
    {
        $form = new Form;

        $form->add([
            'id' => Input::hidden(),
            'name' => Input::text()->required()->attr([
                'placeholder' => _('Tag name')
            ])
        ]);

        foreach ($form as $input) {
            $input->class('form-control');
        }

        return $form;
    }
}
