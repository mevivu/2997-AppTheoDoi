<?php

namespace App\Admin\Services\MemoTheme;

use App\Admin\Repositories\MemoTheme\MemoThemeRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Enums\ActiveStatus;
use Illuminate\Http\Request;

class MemoThemeService implements MemoThemeServiceInterface
{
    protected $repository;
    protected FileService $fileService;

    public function __construct(
        MemoThemeRepositoryInterface $repository,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        if ($request->hasFile('icon')) {
            $data['icon'] = $this->fileService->setFolder('images/memo/themes')
                ->setFile($request->file('icon'))
                ->upload()
                ->getInstance();
        } else {
            $data['icon'] = \App\Traits\ImageSystem::DEFAULT_IMAGE;
        }

        if ($request->hasFile('card_back')) {
            $data['card_back'] = $this->fileService->setFolder('images/memo/themes')
                ->setFile($request->file('card_back'))
                ->upload()
                ->getInstance();
        } else {
            $data['card_back'] = \App\Traits\ImageSystem::DEFAULT_IMAGE;
        }

        return $this->repository->create($data);
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        $theme = $this->repository->findOrFail($data['id']);

        // Xử lý icon
        if ($request->input('reset_icon') == '1') {
            if (!empty($theme->icon) && $theme->icon !== \App\Traits\ImageSystem::DEFAULT_IMAGE) {
                $this->fileService->delete($theme->icon);
            }
            $data['icon'] = \App\Traits\ImageSystem::DEFAULT_IMAGE;
        } elseif ($request->hasFile('icon')) {
            if (!empty($theme->icon) && $theme->icon !== \App\Traits\ImageSystem::DEFAULT_IMAGE) {
                $this->fileService->delete($theme->icon);
            }
            $data['icon'] = $this->fileService->setFolder('images/memo/themes')
                ->setFile($request->file('icon'))
                ->upload()
                ->getInstance();
        } else {
            unset($data['icon']);
        }

        // Xử lý card_back
        if ($request->input('reset_card_back') == '1') {
            if (!empty($theme->card_back) && $theme->card_back !== \App\Traits\ImageSystem::DEFAULT_IMAGE) {
                $this->fileService->delete($theme->card_back);
            }
            $data['card_back'] = \App\Traits\ImageSystem::DEFAULT_IMAGE;
        } elseif ($request->hasFile('card_back')) {
            if (!empty($theme->card_back) && $theme->card_back !== \App\Traits\ImageSystem::DEFAULT_IMAGE) {
                $this->fileService->delete($theme->card_back);
            }
            $data['card_back'] = $this->fileService->setFolder('images/memo/themes')
                ->setFile($request->file('card_back'))
                ->upload()
                ->getInstance();
        } else {
            unset($data['card_back']);
        }

        return $this->repository->update($data['id'], $data);
    }

    public function updatePosition(Request $request): bool
    {
        $positions = $request->input('positions', []);
        if (empty($positions) || !is_array($positions)) {
            return false;
        }

        return $this->repository->updatePosition($positions);
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
