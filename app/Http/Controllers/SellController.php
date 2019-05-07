<?php
namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Item;
use Illuminate\Http\Response;

class SellController extends TransactionController
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
        $validatedRequest = $request->validated();
        $transactionDate = $validatedRequest['transaction_date'];
        $quantity = $validatedRequest['quantity'];

        if (!$this->transactionRepo->canSell($transactionDate, $quantity, $item)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'quantity' => [
                        "Requested quantity is more than inventory"
                    ]
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->transactionRepo->sell($validatedRequest, $item);
        return response()->json(['success' => true], Response::HTTP_CREATED);
    }
}