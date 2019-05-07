<?php
namespace App\Http\Controllers;

use App\Transaction;
use \App\Repositories\Transaction as TransactionRepository;

class TransactionController extends Controller
{
    /** @var TransactionRepository */
    protected $transactionRepo;

    /**
     * SellController constructor.
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transactionRepo = new TransactionRepository($transaction);
    }
}