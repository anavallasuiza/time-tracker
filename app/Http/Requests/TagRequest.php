<?php


namespace App\Http\Requests;


class TagRequest extends Request
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
        return ['name' => 'required|string'];
    }
}