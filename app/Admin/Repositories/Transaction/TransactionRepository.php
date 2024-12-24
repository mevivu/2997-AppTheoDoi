<?php

namespace App\Admin\Repositories\Transaction;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Transaction;

class TransactionRepository extends EloquentRepository implements TransactionRepositoryInterface
{
    public function getModel(): string
    {
        return Transaction::class;
    }
}
