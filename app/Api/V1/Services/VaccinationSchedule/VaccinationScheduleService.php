<?php

namespace App\Api\V1\Services\VaccinationSchedule;

use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Permission\PermissionType;
use Exception;
use Illuminate\Http\Request;

class VaccinationScheduleService implements VaccinationScheduleServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected VaccinationScheduleRepositoryInterface $repository;

    protected FileService $fileService;


    public function __construct(
        VaccinationScheduleRepositoryInterface $repository,
        FileService                            $fileService
    )
    {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $date = $data['performed_on'] ?? null;
        $childId = $data['child_id'];
        $query = $this->repository->getByQueryBuilder(
            [
                'type' => PermissionType::USER
            ]
        );
        if ($childId) {
            $query = $query->whereHas('children', function ($q) use ($childId) {
                $q->where('children.id', $childId);
            });
        }
        if (!empty($date)) {
            $query->whereDate('performed_on', '=', date('Y-m-d', strtotime($date)));
        }
        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $childId = $data['child_id'];
        $image = $data['image'];
        $data['image'] = $this->fileService->uploadAvatar('images/vaccinations', $image);
        $vaccinationSchedule = $this->repository->create($data);
        if ($vaccinationSchedule && $childId) {
            $vaccinationSchedule->children()->attach($childId);
        }

        return $vaccinationSchedule;
    }


    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $image = $data['image'];
        $vaccinationSchedule = $this->repository->findOrFail($data['id']);
        $data['image'] = $this->fileService->uploadAvatar('images/vaccinations', $image, $vaccinationSchedule->image);

        $vaccinationSchedule->update($data);

        return $vaccinationSchedule;
    }


    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $vaccinationSchedule = $this->repository->findOrFail($id);
        $this->fileService->deleteModelImages($vaccinationSchedule, ['image']);
        $this->repository->delete($id);
    }
}
