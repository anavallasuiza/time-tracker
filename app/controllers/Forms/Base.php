<?php
namespace App\Controllers\Forms;

use View;

use App\Libs;

use FormManager\Form;
use FormManager\Inputs\Input;
use FormManager\Fields\Field;

class Base extends Form
{
    public function __construct()
    {
        return $this->method('post');
    }

    public function wrapperInputs()
    {
        foreach ($this as $input) {
            self::wrapperInput($input);
        }

        return $this;
    }

    protected static function wrapperInput($field)
    {
        if (empty($field->input)) {
            return $field;
        }

        $type = $field->input->getElementName();
        $type = ($type === 'input') ? $field->attr('type') : $type;

        $method = 'wrapper'.$type;

        if (method_exists(__CLASS__, $method)) {
            return self::$method($field);
        }

        return self::wrapperDefault($field);
    }

    protected static function wrapperDefault($input)
    {
        $input->label($input->attr('placeholder'));
        $input->addClass('form-control');

        $input->render(function ($input, $label, $labelError) {
            return '<div class="form-group">'.$label.$input.'</div>';
        });
    }

    protected static function wrapperHidden($input)
    {
        return $input;
    }

    protected static function wrapperCheckbox($input)
    {
        if ($input->attr('class') === 'bootstrap-switch') {
            return self::wrapperCheckboxStyled($input);
        }
    }

    protected static function wrapperCheckboxStyled($input)
    {
        $input->label($input->attr('placeholder'));
        $input->addClass('form-control');
        $input->attr('data-on-text', _('Yes'));
        $input->attr('data-off-text', _('No'));

        $input->render(function ($input, $label, $labelError) {
            return '<div class="form-group">'.$label
                .'<div>'.$input.'</div></div>';
        });
    }
}