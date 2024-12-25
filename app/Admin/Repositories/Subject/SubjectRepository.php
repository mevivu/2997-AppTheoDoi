<?php

namespace App\Admin\Repositories\Subject;

use App\Admin\Repositories\EloquentRepository;

use App\Enums\ActiveStatus;
use App\Models\Subject;

class SubjectRepository extends EloquentRepository implements SubjectRepositoryInterface
{
    public function getModel(): string
    {
        return Subject::class;
    }
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10)
    {
        $this->instance = $this->model->where('status', '=', ActiveStatus::Active)
            ->where('name', 'like', '%' . $keySearch . '%');

        $this->applyFilters($meta);
        return $this->instance->limit($limit)->get();
    }


}
