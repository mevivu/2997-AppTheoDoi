<?php

namespace App\Api\V1\Repositories\Exercise;

use App\Admin\Repositories\Exercise\ExerciseRepository as AdminRepository;
use App\Api\V1\Repositories\Clinic\ClinicRepositoryInterface;
use App\Enums\ActiveStatus;


class ExerciseRepository extends AdminRepository implements ExerciseRepositoryInterface
{
    /**
     * Lấy danh sách bài tập
     *
     * @param int $limit
     * @param int $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */


    public function index(int $limit = 10, int $page = 1)
    {
        // TODO: Implement index() method.
        return $this->model->where('status', ActiveStatus::Active)->paginate($limit, ['*'], 'page', $page);
    }
}
