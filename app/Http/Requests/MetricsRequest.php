<?php

namespace App\Http\Requests;

class MetricsRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d'
        ];
    }
}