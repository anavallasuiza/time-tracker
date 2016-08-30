<?php
namespace App\Http\Controllers\Forms;

use FormManager\Inputs\Input;
use FormManager\Fields\Field;

class Activity
{
    public function edit ()
    {
        return (new Base())->add([
            'id' => Input::hidden(),
            'name' => Field::text()->required()->attr([
                'placeholder' => _('Activity name (exactly as defined in Basecamp)')
            ]),
            'archived' => Field::checkbox()->attr([
                'class' => 'bootstrap-switch',
                'placeholder' => _('Archived'),
                'value' => 1
            ]),
            'total_hours' => Field::text()->disabled()->attr([
                'class' => 'text-center',
                'placeholder' => _('Estimated hours')
            ])
        ])->wrapperInputs();
    }
}
