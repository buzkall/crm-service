<?php

namespace App\Http\Requests;

class CustomerRequest extends BaseRequest
{
    public function rules()
    {
        if ($this->getMethod() === 'PUT') { // updating
            return [
                'name'       => 'string',
                'surname'    => 'string',
                'photo_file' => 'file',
            ];
        }
        return [
            'name'       => 'required',
            'surname'    => 'required',
            'photo_file' => 'required|file',
        ];
    }
}
