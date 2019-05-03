<?php
namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;

class PurchaseController extends Controller
{
    protected static $defaultPurchasePrice = 0.20;

    public function create(TransactionRequest $request, $item)
    {
        return response()->json(['status' => 'OK']);
    }
}