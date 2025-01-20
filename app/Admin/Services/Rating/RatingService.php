<?php

namespace App\Admin\Services\Rating;

use App\Admin\Repositories\Rating\RatingRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;

class RatingService implements RatingServiceInterface
{
    protected RatingRepositoryInterface $repository;

    public function __construct(
        RatingRepositoryInterface $repository
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
