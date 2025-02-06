<?php

namespace App\Api\V1\Services\Transaction;


use App\Api\V1\Repositories\Transaction\TransactionRepositoryInterface;

use App\Api\V1\Support\AuthServiceApi;
use Illuminate\Http\Request;


class TransactionService implements TransactionServiceInterface
{
    use AuthServiceApi;

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


    public function index(Request $request)
    {
        $data = $request->validated();
        $userId = $this->getCurrentUserId();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;

        $query = $this->repository->getByQueryBuilder([
            'user_id' => $userId,
        ]);
        return $query->paginate($limit, ['*'], 'page', $page);
    }


}
