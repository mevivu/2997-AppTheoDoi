<?php

namespace App\Api\V1\Services\VaccinationSchedule;

use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface;
use App\Api\V1\Services\VaccinationSchedule\VaccinationScheduleServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $query = $this->repository->getQueryBuilder();
        return $query->paginate($limit, ['*'], 'page', $page);
    }
    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $data['image'] = $this->uploadPhotos($request->file('image') ?? []);
        return $this->repository->create($data);
    }




    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $vaccinationSchedule = $this->repository->findOrFail($data['id']);
        $data['image'] = $this->uploadPhotos($request->file('image') ?? [], $vaccinationSchedule);

        $vaccinationSchedule->update($data);

        return $vaccinationSchedule;
    }

    protected function uploadPhotos($photos, $model = null): string
    {
        $paths = [];

        // Xử lý xóa ảnh cũ nếu có
        if ($model && $model->image) {
            $oldPaths = json_decode($model->image);
            if (is_array($oldPaths)) {
                foreach ($oldPaths as $path) {
                    $path = preg_replace('#/+#', '/', $path);
                    $this->fileService->delete($path);
                }
            }
        }

        // Lặp qua các ảnh được upload và xử lý
        foreach ($photos as $photo) {
            if ($photo->isValid()) {
                // Lưu ảnh vào thư mục public/uploads/files
                $uploadedPath = $photo->storeAs('uploads/files', $photo->hashName(), 'public'); // Đảm bảo sử dụng 'public' disk

                // Trả về đường dẫn bắt đầu bằng '/public'
                $formattedPath = '/public/' . $uploadedPath;

                // Thêm đường dẫn đã được xử lý vào mảng paths
                $paths[] = $formattedPath;
            } else {
                Log::warning('Uploaded file is invalid.', ['file' => $photo->getClientOriginalName()]);
            }
        }

        // Trả về mảng đường dẫn dưới dạng JSON
        return json_encode($paths);
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
