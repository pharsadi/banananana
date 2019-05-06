<?php
namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Item;
use App\Transaction;
use \App\Repositories\Transaction as TransactionRepository;
use Illuminate\Http\Response;

class PurchaseController extends Controller
{
    /** @var TransactionRepository  */
    protected $transactionRepo;

    public function __construct(Transaction $transaction)
    {
        $this->transactionRepo = new TransactionRepository($transaction);
    }

    public function create(TransactionRequest $request, Item $item)
    {
        $this->transactionRepo->purchase($request->validated(), $item);
        return response()->json(['success' => true], Response::HTTP_CREATED);
    }
}