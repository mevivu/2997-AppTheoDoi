<?php

namespace App\Api\V1\Repositories\Setting;
use App\Admin\Repositories\EloquentRepository;
use App\Models\Setting;


class SettingRepository extends EloquentRepository implements SettingRepositoryInterface
{
    public function getModel()
    {
        return Setting::class;
    }

    public function getByGroup(array $group)
    {
        $this->instance = $this->model->whereIn('group', $group)->get();
        return $this->instance;
    }
}