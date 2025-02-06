<?php

namespace App\Api\V1\Services\Transaction;


use Illuminate\Http\Request;

interface TransactionServiceInterface
{
    public function index(Request $request);

}
