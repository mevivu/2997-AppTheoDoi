<?php

namespace App\Api\V1\Services\VaccinationSchedule;

use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\ActiveStatus;
use App\Enums\Permission\PermissionType;
use App\Models\Child;
use App\Models\VaccinationSchedule;
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

        // Build query với điều kiện lọc
        $query = $this->repository->getByQueryBuilder([
            'child_id' => $childId,
            'type' => PermissionType::USER,
        ]);

        if (!empty($date)) {
            $query->whereDate('performed_on', '=', date('Y-m-d', strtotime($date)));
        }

        // Eager load quan hệ vaccinationType
        $query->with('vaccinationType');

        // Paginate trước
        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        // Sắp xếp theo position của vaccinationType sau khi paginate
        $sorted = $paginator->getCollection()
            ->sortBy(function ($item) {
                return optional($item->vaccinationType)->position ?? 9999;
            })
            ->values();

        // Gán lại collection đã sort vào paginator
        $paginator->setCollection($sorted);

        return $paginator;
    }




    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        if($image){
            $data['image'] = $this->fileService->uploadAvatar('images/vaccinations', $image);
        }
        return $this->repository->create($data);
    }


    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $image = $data['image'] ?? null;
        $vaccinationSchedule = $this->repository->findOrFail($data['id']);
        if($image){
            $data['image'] = $this->fileService->uploadAvatar('images/vaccinations', $image, $vaccinationSchedule->image);

        }
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

    /**
     * Khởi tạo sổ tiêm chủng cho hồ sơ con (Lazy Initialization).
     *
     * Chỉ chạy khi user chủ động bấm "Mở sổ tiêm chủng" trên app.
     * Sao chép toàn bộ vaccination schedules của admin → child.
     *
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function initialize(Request $request): array
    {
        $childId = $request->input('child_id');

        $child = Child::findOrFail($childId);

        // Kiểm tra đã khởi tạo chưa → tránh tạo trùng
        if ($child->vaccination_initialized) {
            return [
                'already_initialized' => true,
                'message' => 'Sổ tiêm chủng đã được kích hoạt trước đó.',
            ];
        }

        // Sao chép toàn bộ vaccination schedules từ admin → child
        $adminSchedules = VaccinationSchedule::where('type', PermissionType::ADMIN)
            ->where('status', ActiveStatus::Active)
            ->get();

        $createdCount = 0;
        foreach ($adminSchedules as $schedule) {
            VaccinationSchedule::create([
                'child_id' => $child->id,
                'name' => $schedule->name,
                'description' => $schedule->description,
                'image' => $schedule->image,
                'performed_on' => $schedule->performed_on,
                'vaccination_status' => $schedule->vaccination_status,
                'vaccination_type_id' => $schedule->vaccination_type_id,
                'type' => PermissionType::USER,
            ]);
            $createdCount++;
        }

        // Đánh dấu đã khởi tạo
        $child->update(['vaccination_initialized' => true]);

        return [
            'already_initialized' => false,
            'message' => 'Đã kích hoạt sổ tiêm chủng thành công.',
            'total_schedules' => $createdCount,
        ];
    }
}
