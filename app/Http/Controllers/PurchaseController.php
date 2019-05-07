<?php
namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Item;
use Illuminate\Http\Response;

class PurchaseController extends TransactionController
{
    /**
     * POST call
     *
     * @param TransactionRequest $request
     * @param Item $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(TransactionRequest $request, Item $item)
    {
        $this->transactionRepo->purchase($request->validated(), $item);
        return response()->json(['success' => true], Response::HTTP_CREATED);
    }
}