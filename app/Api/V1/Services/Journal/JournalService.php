<?php

namespace App\Api\V1\Services\Journal;

use App\Admin\Services\File\FileService;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Repositories\Journal\JournalRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Models\Journal;
use App\Models\JournalMilestone;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JournalService implements JournalServiceInterface
{
    use AuthSupport, AuthServiceApi;

    protected array $data;

    protected JournalRepositoryInterface $repository;

    protected FileService $fileService;

    public function __construct(
        JournalRepositoryInterface $repository,
        FileService                $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function index(Request $request)
    {
        $data = $request->validated();
        $type = $data['type'];

        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;
        $date = $data['date'] ?? null;

        $query = $this->repository->getByQueryBuilder(
            [
                'type' => $type,
                'child_id' => $data['child_id'],
            ],
            ['milestone'],
            ['created_at', 'desc']
        );

        // Thứ tự phụ theo id desc khi ngày sự kiện trùng nhau
        $query->orderBy('id', 'desc');

        if ($date) {
            $date = date('Y-m-d', strtotime($date));
            $query->whereDate('created_at', '=', $date);
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Lấy chi tiết nhật ký kèm kiểm tra quyền sở hữu (B1 IDOR)
     */
    public function show(int $id): Journal
    {
        return $this->getAuthorizedJournal($id);
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object
    {
        $data = $request->validated();

        // Tự động điền tiêu đề từ cột mốc gợi ý nếu người dùng để trống
        if (!empty($data['milestone_id']) && empty($data['title'])) {
            $milestone = JournalMilestone::find($data['milestone_id']);
            if ($milestone) {
                $data['title'] = $milestone->name;
            }
        }

        $image = $data['image'] ?? null;
        if ($image) {
            $data['image'] = $this->uploadPhotos($request->file('image') ?? []);
        }

        try {
            return $this->repository->create($data);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new BadRequestException('Bé đã có nhật ký cho cột mốc này.');
            }
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object
    {
        $data = $request->validated();
        $journal = $this->getAuthorizedJournal((int)$data['id']);

        if (!empty($data['milestone_id']) && empty($data['title']) && empty($journal->title)) {
            $milestone = JournalMilestone::find($data['milestone_id']);
            if ($milestone) {
                $data['title'] = $milestone->name;
            }
        }

        $image = $data['image'] ?? null;
        if ($image) {
            $data['image'] = $this->uploadPhotos($request->file('image') ?? [], $journal);
        }

        try {
            $journal->update($data);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new BadRequestException('Bé đã có nhật ký khác cho cột mốc này.');
            }
            throw $e;
        }

        return $journal->fresh(['milestone']);
    }

    /**
     * Kiểm tra quyền sở hữu nhật ký theo con của user hiện tại
     *
     * @throws NotFoundException
     */
    public function getAuthorizedJournal(int $id): Journal
    {
        $userId = auth('api')->id() ?? auth()->id();
        $journal = $this->repository->findOrFail($id);

        if (!$journal->relationLoaded('child')) {
            $journal->load('child');
        }

        if (!$journal->child || (int)$journal->child->user_id !== (int)$userId) {
            throw new NotFoundException('Nhật ký không tìm thấy.');
        }

        if (!$journal->relationLoaded('milestone')) {
            $journal->load('milestone');
        }

        return $journal;
    }

    protected function uploadPhotos($photos, $model = null): string
    {
        $paths = [];
        if ($model && $model->image) {
            $oldPaths = json_decode($model->image);
            if (is_array($oldPaths)) {
                foreach ($oldPaths as $path) {
                    $path = preg_replace('#/+#', '/', $path);
                    $this->fileService->delete($path);
                }
            }
        }
        foreach ($photos as $photo) {
            if ($photo->isValid()) {
                $uploadedPath = $this->fileService->uploadAvatar('images/journals', $photo);
                $formattedPath = '/' . trim($uploadedPath, '/');
                $paths[] = $formattedPath;
            } else {
                Log::warning('Uploaded file is invalid.', ['file' => $photo->getClientOriginalName()]);
            }
        }
        return json_encode($paths);
    }

    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $journal = $this->getAuthorizedJournal((int)$id);
        $this->fileService->deleteModelImages($journal, ['image']);
        $this->repository->delete($id);
    }
}
