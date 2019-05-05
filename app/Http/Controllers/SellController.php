<?php
namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
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

    public function create(TransactionRequest $request, $item)
    {
        $this->transactionRepo->sell($request->validated(), $item);
        return response()->json(['status' => 'OK'], Response::HTTP_CREATED);
    }
}