<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'     => 'required|min:3',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = (new Controller())->sendError('Validation error', $validator->errors(), 422);
        throw new ValidationException($validator, $response);
    }
}
