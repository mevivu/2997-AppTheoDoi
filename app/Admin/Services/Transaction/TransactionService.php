<?php

namespace App\Admin\Services\Transaction;


use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class TransactionService implements TransactionServiceInterface
{
    use Setup, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected TransactionRepositoryInterface $repository;


    public function __construct(
        TransactionRepositoryInterface $repository,

    )
    {
        $this->repository = $repository;
    }


    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();
        $questionIds = $data['question_ids'] ?? [];
        $quiz = $this->repository->create($data);
        if (!empty($data['question_ids'])) {
            $quiz->questions()->attach($questionIds);
        }
        return $quiz;
    }


}
