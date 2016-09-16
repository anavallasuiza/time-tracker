<?php


namespace App\Http\Requests;


class UserRequest extends Request
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
            'name' => 'required|string',
            'user' => 'required|string',
            'email' => 'required|email',
            'password' => 'string|min:3|confirmed',
            'password_confirmation' => 'string|min:3',
            'api_key' => 'required|string',
            'store_hours' => 'boolean',
            'enabled' => 'boolean',
        ];
    }
}