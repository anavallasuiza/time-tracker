<?php
namespace App\Controllers\Forms;

use FormManager\Form;
use FormManager\Inputs\Input;

class Fact {
    public function add ()
    {
        return (new Form)->add([
            'activity' => Input::text()->required()->attr([
                'pattern' => '[0-9]+'
            ]),
            'tag' => Input::text()->required()->attr([
                'pattern' => '[0-9]+'
            ]),
            'start' => Input::text()->required()->attr([
                'pattern' => '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}'
            ]),
            'end' => Input::text()->required()->attr([
                'pattern' => '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}'
            ])
        ]);
    }

    public function edit ()
    {
        return (new Form)->add([
            'activity' => Input::text()->required()->attr([
                'pattern' => '^[0-9]+$'
            ]),
            'tag' => Input::text()->required()->attr([
                'pattern' => '^[0-9]+$'
            ]),
            'id' => Input::text()->required()->attr([
                'pattern' => '^[0-9]+$'
            ]),
            'start' => Input::text()->required()->attr([
                'pattern' => '^[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}$'
            ]),
            'end' => Input::text()->required()->attr([
                'pattern' => '^[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}$'
            ])
        ]);
    }
}
