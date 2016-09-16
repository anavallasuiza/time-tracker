<?php


namespace App\Http\Requests;


class FactRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'activity' => 'required|integer',
            'tag' => 'required',
            'start' => 'required',
            'end' => 'required',
            'time' => 'required|regex:/[0-9]+:[0-9]+/'
        ];
    }
}