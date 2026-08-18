<?php

namespace App\Api\V1\Services\Child;

use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Child\ChildRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Child\BornStatus;
use App\Enums\Child\ChildStatus;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

class ChildService implements ChildServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected ChildRepositoryInterface $repository;

    protected FileService $fileService;

    public function __construct(
        ChildRepositoryInterface $repository,
        FileService              $fileService
    )
    {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    /**
     * Lấy danh sách ảnh đại diện mặc định của trẻ
     */
    public function getDefaultAvatars(): array
    {
        $avatars = [];
        for ($i = 1; $i <= 5; $i++) {
            $relativePath = "/public/assets/images/children/child_avatar{$i}.png";
            $avatars[] = [
                'id' => $i,
                'name' => "Avatar {$i}",
                'path' => $relativePath,
                'url' => asset($relativePath),
            ];
        }
        return $avatars;
    }

    /**
     * Xử lý avatar linh hoạt: Hỗ trợ cả UploadedFile (App cũ / Thư viện ảnh) và String (App mới / Avatar mặc định)
     */
    public function processAvatar(mixed $avatar, ?string $oldAvatar = null): ?string
    {
        if (!$avatar) {
            return null;
        }

        // Case 1: UploadedFile (Multipart upload từ camera/gallery hoặc app cũ)
        if ($avatar instanceof UploadedFile) {
            return $this->fileService->uploadAvatar('images/children', $avatar, $oldAvatar);
        }

        // Case 2: String path / URL / Default Avatar name
        if (is_string($avatar)) {
            $avatar = trim($avatar);
            if (empty($avatar)) {
                return null;
            }

            // Nếu string chứa tên file mặc định như child_avatar1.png ... child_avatar5.png
            if (preg_match('/child_avatar([1-5])\.png$/i', $avatar, $matches)) {
                return "/public/assets/images/children/child_avatar{$matches[1]}.png";
            }

            // Nếu là URL đầy đủ
            if (filter_var($avatar, FILTER_VALIDATE_URL)) {
                $parsedPath = parse_url($avatar, PHP_URL_PATH);
                if ($parsedPath) {
                    return '/' . ltrim($parsedPath, '/');
                }
            }

            // Nếu là relative path
            if (str_starts_with($avatar, '/public/') || str_starts_with($avatar, 'public/')) {
                return '/' . ltrim($avatar, '/');
            }

            return '/' . ltrim($avatar, '/');
        }

        return null;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();
        $avatar = $data['avatar'] ?? null;
        $data['user_id'] = $this->getCurrentUserId();

        if ($data['is_born'] == BornStatus::Born->value) {
            $birthday = $data['birthday'];
            $birthday = new Carbon($birthday);
            $currentDate = Carbon::now();
            $month = $currentDate->diffInDays($birthday) / 30.5;
            $age = $currentDate->diffInDays($birthday) / 365.3;
            $data['age'] = $age;
            $data['month'] = $month;
        } else {
            $data['due_date'] = $data['birthday'];
            $data['birthday'] = null;
            $data['age'] = null;
            $data['month'] = null;
        }

        if ($avatar) {
            $data['avatar'] = $this->processAvatar($avatar);
        } else {
            $data['avatar'] = '/public/assets/images/children/child_avatar1.png';
        }

        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $child = $this->repository->find($data['id']);
        $avatar = $data['avatar'] ?? null;
        $birthday = $data['birthday'] ?? null;

        if ($data['is_born'] == BornStatus::Born->value) {
            $birthday = new Carbon($birthday);
            $currentDate = Carbon::now();
            $month = $currentDate->diffInDays($birthday) / 30.5;
            $age = $currentDate->diffInDays($birthday) / 365.3;
            $data['age'] = $age;
            $data['month'] = $month;
            $data['due_date'] = null;
        } else {
            $data['due_date'] = $data['birthday'];
            $data['birthday'] = null;
            $data['age'] = null;
            $data['month'] = null;
        }

        if ($avatar) {
            $data['avatar'] = $this->processAvatar($avatar, $child->avatar);
        } else {
            unset($data['avatar']);
        }

        return $this->repository->update($data['id'], $data);
    }

    public function index(Request $request)
    {
        $data = $request->validated();
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? 10;
        $query = $this->repository->getByQueryBuilder([
            'status' => ChildStatus::Active,
            'user_id' => $this->getCurrentUserId(),
        ]);
        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws Exception
     */
    public function syncChildren(Request $request): void
    {
        $data = $request->validated();
        $children = $data['children'] ?? [];
        $userId = $this->getCurrentUserId();

        foreach ($children as $childData) {
            $id = $childData['id'] ?? null;
            $childData['user_id'] = $userId;
            $birthday = $childData['birthday'] ?? null;
            $born = $childData['is_born'];

            if (isset($id) && $this->repository->exists($id)) {
                $child = $this->repository->findOrFail($id);
                if (isset($childData['avatar']) && $childData['avatar']) {
                    $childData['avatar'] = $this->processAvatar($childData['avatar'], $child->avatar);
                }

                if ($born == BornStatus::Born->value) {
                    $this->calculateAgeAndMonth($birthday, $childData);
                } else {
                    $childData['birthday'] = null;
                    $childData['age'] = null;
                    $childData['month'] = null;
                }
                $this->repository->update($id, $childData);
            } else {
                if ($born == BornStatus::Born->value) {
                    $this->calculateAgeAndMonth($birthday, $childData);
                } else {
                    $childData['birthday'] = null;
                    $childData['age'] = null;
                    $childData['month'] = null;
                }
                if (isset($childData['avatar']) && $childData['avatar']) {
                    $childData['avatar'] = $this->processAvatar($childData['avatar']);
                } else {
                    $childData['avatar'] = '/public/assets/images/children/child_avatar1.png';
                }
                $this->repository->create($childData);
            }
        }
    }

    /**
     * Tính toán tuổi và tháng từ ngày sinh.
     *
     * @param string $birthday
     * @param array $childData
     * @return void
     */
    private function calculateAgeAndMonth(string $birthday, array &$childData): void
    {
        $birthday = new Carbon($birthday);
        $currentDate = Carbon::now();

        $month = $currentDate->diffInDays($birthday) / 30.5;
        $age = $currentDate->diffInDays($birthday) / 365.3;

        $childData['age'] = $age;
        $childData['month'] = $month;
    }
}
