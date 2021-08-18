<?php

namespace App\Http\Requests;

class LoginRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'email'       => 'required|string|email',
            'password'    => 'required|string',
            'remember_me' => 'boolean',
        ];
    }
}
