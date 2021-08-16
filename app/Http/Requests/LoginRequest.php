<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email'       => 'required|string|email',
            'password'    => 'required|string',
            'remember_me' => 'boolean',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = (new Controller())->sendError('Validation error', $validator->errors(), 422);
        throw new ValidationException($validator, $response);
    }
}
