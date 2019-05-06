<?php
namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Item;
use App\Transaction;
use \App\Repositories\Transaction as TransactionRepository;
use Illuminate\Http\Response;

class SellController extends Controller
{
    /** @var TransactionRepository  */
    protected $transactionRepo;

    public function __construct(Transaction $transaction)
    {
        $this->transactionRepo = new TransactionRepository($transaction);
    }

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
                        "Sold out"
                    ]
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->transactionRepo->sell($validatedRequest, $item);
        return response()->json(['success' => true], Response::HTTP_CREATED);
    }
}