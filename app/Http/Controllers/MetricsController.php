<?php
namespace App\Http\Controllers;

use App\Http\Requests\MetricsRequest;
use App\Item;
use App\Transaction;
use \App\Repositories\Transaction as TransactionRepository;

class MetricsController extends Controller
{
    /** @var TransactionRepository  */
    protected $transactionRepo;

    public function __construct(Transaction $transaction)
    {
        $this->transactionRepo = new TransactionRepository($transaction);
    }

    public function read(MetricsRequest $request, Item $item)
    {
        $validatedRequest = $request->validated();
        $startDate = $validatedRequest['start_date'];
        $endDate = $validatedRequest['end_date'];

    }
}