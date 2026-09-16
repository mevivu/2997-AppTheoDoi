<?php

namespace App\Admin\Services\MemoRating;

use App\Admin\Repositories\MemoRating\MemoRatingRepositoryInterface;
use Illuminate\Http\Request;

class MemoRatingService implements MemoRatingServiceInterface
{
    protected $repository;

    public function __construct(
        MemoRatingRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function actionMultipleRecords(Request $request): bool
    {
        $data = $request->all();

        if ($data['action'] === 'delete') {
            foreach ($data['id'] as $value) {
                $this->repository->delete($value);
            }
            return true;
        }

        return false;
    }
}
