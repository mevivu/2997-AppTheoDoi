<?php

namespace App\Admin\Services\RatingPQ;

use App\Admin\Repositories\RatingPQ\RatingPQRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;

class RatingPQService implements RatingPQServiceInterface
{
    protected RatingPQRepositoryInterface $repository;

    public function __construct(
        RatingPQRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }


    /**
     * @throws Exception
     */
    public function actionMultipleRecords(Request $request): bool
    {
        $data = $request->all();

        switch ($data['action']) {
            case ActiveStatus::Deleted->value:
                foreach ($data['id'] as $value) {
                    $this->repository->delete($value);
                }
                return true;

            default:
                return false;
        }
    }

}
