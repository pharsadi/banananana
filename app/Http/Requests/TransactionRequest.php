<?php

namespace App\Http\Requests;


class TransactionRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'price' => 'nullable|numeric',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date_format:Y-m-d'
        ];
    }
}