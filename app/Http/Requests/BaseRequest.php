<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $data = [
            'success' => false,
            'errors' => $validator->errors()->messages()
        ];

        $response = response()->json($data, Response::HTTP_BAD_REQUEST);
        throw new HttpResponseException($response);
    }
}