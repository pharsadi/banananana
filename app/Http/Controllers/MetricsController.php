<?php
namespace App\Http\Controllers;

use App\Http\Requests\MetricsRequest;
use App\Item;
use Illuminate\Http\Response;

class MetricsController extends TransactionController
{
    /**
     * GET call
     *
     * @param MetricsRequest $request
     * @param Item $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(MetricsRequest $request, Item $item)
    {
        $validatedRequest = $request->validated();
        $startDate = $validatedRequest['start_date'];
        $endDate = $validatedRequest['end_date'];

        $metrics = $this->transactionRepo->metrics($startDate, $endDate, $item);

        return response()->json($metrics, Response::HTTP_OK);
    }
}