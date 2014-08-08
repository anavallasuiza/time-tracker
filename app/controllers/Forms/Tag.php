<?php
namespace App\Controllers\Forms;

use FormManager\Inputs\Input;
use FormManager\Fields\Field;

class Tag
{
    public function edit ()
    {
        return (new Base())->add([
            'id' => Input::hidden(),
            'name' => Field::text()->required()->attr([
                'placeholder' => _('Name')
            ])
        ])->wrapperInputs();
    }
}
