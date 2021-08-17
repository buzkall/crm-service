<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $this->merge(['password' => bcrypt($this->password)]);
    }

    public function rules()
    {
        return [
            'name'     => 'string',
            'email'    => 'string',
            'password' => 'string',
        ];
    }
}
