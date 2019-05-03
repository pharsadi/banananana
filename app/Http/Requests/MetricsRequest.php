<?php

namespace App\Http\Requests;

class MetricsRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'startDate' => 'required|date_format:Y-m-d',
            'endDate' => 'required|date_format:Y-m-d'
        ];
    }
}