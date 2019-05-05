<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'item', 'price_per_item', 'transaction_type', 'transaction_date', 'quantity'
    ];
}
