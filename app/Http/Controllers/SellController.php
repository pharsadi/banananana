<?php
namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;

class SellController extends Controller
{
    protected static $defaultSellPrice = 0.35;

    public function create(TransactionRequest $request, $item)
    {
        return response()->json(['status' => 'OK']);
    }
}