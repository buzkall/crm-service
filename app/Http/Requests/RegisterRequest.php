<?php

namespace App\Http\Requests;

class RegisterRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name'     => 'required|min:3',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ];
    }
}
