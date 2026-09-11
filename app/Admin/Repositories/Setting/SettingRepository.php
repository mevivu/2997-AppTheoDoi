<?php

namespace App\Admin\Repositories\Setting;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\{Cache, DB};

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

    public function updateMultipleRecord(array $data)
    {
        Cache::forget(Setting::CACHE_KEY_GET_ALL);

        DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                $this->model->where('setting_key', $key)->update(['plain_value' => $value]);
            }
        });

        return true;
    }

    public function getPlainValue(string $key)
    {
        return $this->findByField('setting_key', $key)->plain_value;
    }
}
