<?php
namespace App\Controllers\Forms;

use FormManager\Form;
use FormManager\Inputs\Input;

class Activity {
    public function edit ()
    {
        $form = new Form;

        $form->add([
            'id' => Input::hidden(),
            'name' => Input::text()->required()->attr([
                'placeholder' => _('Activity name (exactly as defined in Basecamp)')
            ]),
            'archived' => Input::checkbox()->attr([
                'value' => 1
            ])
        ]);

        foreach ($form as $input) {
            if ($input->attr('type') !== 'checkbox') {
                $input->class('form-control');
            }
        }

        return $form;
    }
}
