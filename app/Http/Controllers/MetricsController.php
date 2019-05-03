<?php
namespace App\Http\Controllers;

use App\Http\Requests\MetricsRequest;

class MetricsController extends Controller
{
    public function read(MetricsRequest $request, $item)
    {
        echo $item;
        echo $request->get('startDate');

    }
}