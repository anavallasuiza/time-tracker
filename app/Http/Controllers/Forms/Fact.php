<?php
namespace App\Http\Controllers\Forms;

use App\Libs;

use FormManager\Form;
use FormManager\Inputs\Input;

class Fact
{
    private function getForm()
    {
        $form = new Form;

        $form->add([
            'activity' => Input::text()->required()->attr([
                'pattern' => '[0-9]+'
            ]),
            'tag' => Input::text()->required()->attr([
                'pattern' => '[0-9]+'
            ]),
            'start' => Input::text()->required(),
            'end' => Input::text()->required(),
            'time' => Input::text()->required()->attr([
                'pattern' => '[0-9]+:[0-9]+'
            ])
        ]);

        $pattern = '[0-9]{2}/[0-9]{2}/[0-9]{4}.*';

        if (Libs\Auth::user()->store_hours) {
            $pattern .= ' [0-9]{2}:[0-9]{2}';
        }

        $form['start']->attr('pattern', $pattern);
        $form['end']->attr('pattern', $pattern);

        return $form;
    }

    public function add ()
    {
        return $this->getForm();
    }

    public function edit ()
    {
        $form = $this->getForm();

        return $form->add(['id' => Input::text()->required()->attr([
            'pattern' => '^[0-9]+$'
        ])]);
    }
}
