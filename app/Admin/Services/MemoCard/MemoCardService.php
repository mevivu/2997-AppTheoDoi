<?php

namespace App\Admin\Services\MemoCard;

use App\Admin\Repositories\MemoCard\MemoCardRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Enums\ActiveStatus;
use App\Traits\ImageSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemoCardService implements MemoCardServiceInterface
{
    protected $repository;
    protected FileService $fileService;

    public function __construct(
        MemoCardRepositoryInterface $repository,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->fileService->setFolder('images/memo/cards')
                ->setFile($request->file('image'))
                ->upload()
                ->getInstance();
        } else {
            $data['image'] = ImageSystem::DEFAULT_IMAGE;
        }

        if ($request->hasFile('audio')) {
            $data['audio'] = $this->fileService->setFolder('audio/memo')
                ->setFile($request->file('audio'))
                ->upload()
                ->getInstance();
        } else {
            $data['audio'] = null;
        }

        if (!isset($data['position']) || empty($data['position'])) {
            $maxPos = \App\Models\MemoCard::where('memo_theme_id', $data['memo_theme_id'])->max('position');
            $data['position'] = ($maxPos ?? 0) + 1;
        }

        return $this->repository->create($data);
    }

    public function bulkStore(Request $request): int
    {
        $themeId = (int) $request->input('memo_theme_id');
        $files = $request->file('images', []);
        $count = 0;
        $maxPos = \App\Models\MemoCard::where('memo_theme_id', $themeId)->max('position') ?? 0;

        foreach ($files as $file) {
            $imagePath = $this->fileService->setFolder('images/memo/cards')
                ->setFile($file)
                ->upload()
                ->getInstance();

            // Tên thẻ mặc định lấy theo tên file không có đuôi
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanName = ucfirst(str_replace(['_', '-'], ' ', $filename));
            $maxPos++;

            $this->repository->create([
                'memo_theme_id' => $themeId,
                'name' => $cleanName,
                'image' => $imagePath,
                'position' => $maxPos,
                'status' => ActiveStatus::Active->value,
            ]);
            $count++;
        }

        return $count;
    }

    public function updatePosition(Request $request): bool
    {
        $positions = $request->input('positions', []);
        if (!is_array($positions) || empty($positions)) {
            return false;
        }

        return $this->repository->updatePosition($positions);
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $card = $this->repository->findOrFail($data['id']);

        // Xử lý ảnh: Đặt lại mặc định hoặc upload ảnh mới, giữ nguyên nếu không đổi
        if ($request->input('reset_image') == '1') {
            if (!empty($card->image) && $card->image !== ImageSystem::DEFAULT_IMAGE) {
                $this->fileService->delete($card->image);
            }
            $data['image'] = ImageSystem::DEFAULT_IMAGE;
        } elseif ($request->hasFile('image')) {
            if (!empty($card->image) && $card->image !== ImageSystem::DEFAULT_IMAGE) {
                $this->fileService->delete($card->image);
            }
            $data['image'] = $this->fileService->setFolder('images/memo/cards')
                ->setFile($request->file('image'))
                ->upload()
                ->getInstance();
        } else {
            unset($data['image']);
        }

        // Xử lý audio: Xóa hoặc upload audio mới, giữ nguyên nếu không đổi
        if ($request->input('delete_audio') == '1') {
            if (!empty($card->audio)) {
                $this->fileService->delete($card->audio);
            }
            $data['audio'] = null;
        } elseif ($request->hasFile('audio')) {
            if (!empty($card->audio)) {
                $this->fileService->delete($card->audio);
            }
            $data['audio'] = $this->fileService->setFolder('audio/memo')
                ->setFile($request->file('audio'))
                ->upload()
                ->getInstance();
        } else {
            unset($data['audio']);
        }

        return $this->repository->update($data['id'], $data);
    }

    public function actionMultipleRecords(Request $request): bool
    {
        $data = $request->all();

        switch ($data['action']) {
            case ActiveStatus::Active->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Active);
                }
                return true;
            case ActiveStatus::Draft->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Draft);
                }
                return true;
            case ActiveStatus::Deleted->value:
                foreach ($data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', ActiveStatus::Deleted);
                }
                return true;
            default:
                return false;
        }
    }
}
