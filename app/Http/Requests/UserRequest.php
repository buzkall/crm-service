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
        if ($this->getMethod() === 'PUT') { // updating
            return [
                'name'     => 'string',
                'email'    => 'string|unique:users,id',
                'is_admin' => 'boolean',
                'password' => 'string',
            ];
        }
        return [
            'name'     => 'required|string',
            'email'    => 'required|string|unique:users',
            'is_admin' => 'boolean',
            'password' => 'required|string',
        ];
    }
}
