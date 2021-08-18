<?php

namespace App\Http\Requests;

class UserRequest extends BaseRequest
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
            'is_admin' => 'boolean',
            'password' => 'string',
        ];
    }
}
