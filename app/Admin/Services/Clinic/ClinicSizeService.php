<?php

namespace App\Admin\Services\Clinic;

use App\Admin\Repositories\Clinic\ClinicRepositoryInterface;
use App\Admin\Repositories\District\DistrictRepositoryInterface;
use App\Admin\Repositories\Province\ProvinceRepositoryInterface;
use App\Admin\Repositories\Ward\WardRepositoryInterface;
use App\Api\V1\Support\UseLog;
use App\Enums\ActiveStatus;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;

class ClinicSizeService implements ClinicServiceInterface
{
    use Setup, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ClinicRepositoryInterface $repository;

    protected ProvinceRepositoryInterface $provinceRepository;

    protected DistrictRepositoryInterface $districtRepository;

    protected WardRepositoryInterface $wardRepository;


    public function __construct(
        ClinicRepositoryInterface   $repository,
        ProvinceRepositoryInterface $provinceRepository,
        DistrictRepositoryInterface $districtRepository,
        WardRepositoryInterface     $wardRepository,
    )
    {
        $this->repository = $repository;
        $this->provinceRepository = $provinceRepository;
        $this->districtRepository = $districtRepository;
        $this->wardRepository = $wardRepository;
    }

    private function prepareAddressData(array &$data): void
    {
        $data['province_id'] = $this->provinceRepository
            ->findByField('code', $data['province'])->id;
        $data['district_id'] = $this->districtRepository
            ->findByField('code', $data['district'])->id;
        $data['ward_id'] = $this->wardRepository
            ->findByField('code', $data['ward'])->id;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();
        $this->prepareAddressData($data);
        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {

        $data = $request->validated();
        $this->prepareAddressData($data);
        return $this->repository->update($data['id'], $data);
    }

    /**
     * @throws Exception
     */
    public function delete($id): object
    {
        return $this->repository->delete($id);

    }

    public function actionMultipleRecords(Request $request): bool
    {
        $this->data = $request->all();

        switch ($this->data['action']) {
            case ActiveStatus::Active->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Active);
                }
                return true;
            case ActiveStatus::Draft->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Draft);
                }
                return true;
            case ActiveStatus::Deleted->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Deleted);
                }
                return true;

            default:
                return false;
        }
    }
}
