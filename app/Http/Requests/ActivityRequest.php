<?php


namespace App\Http\Requests;


class ActivityRequest extends Request
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
            'archived' => 'boolean',
            'id_clients' => 'required|integer',
            'total_hours' => 'integer/**/'
        ];
    }
}