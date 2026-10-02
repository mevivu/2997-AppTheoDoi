<?php

namespace App\Admin\Http\Controllers\Introduction;

use App\Admin\DataTables\Introduction\IntroductionDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Introduction\IntroductionRequest;
use App\Admin\Repositories\Introduction\IntroductionRepositoryInterface;
use App\Admin\Services\Introduction\IntroductionServiceInterface;
use App\Enums\DefaultStatus;
use App\Enums\Introduction\IntroductionSectionType;
use App\Traits\ResponseController;
use App\Traits\RouteAdminSystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IntroductionController extends Controller
{
    use ResponseController;

    public function __construct(
        IntroductionRepositoryInterface $repository,
        IntroductionServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.introductions.index',
            'create' => 'admin.introductions.create',
            'edit' => 'admin.introductions.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => RouteAdminSystem::INTRODUCTION_INDEX,
            'create' => RouteAdminSystem::INTRODUCTION_CREATE,
            'edit' => RouteAdminSystem::INTRODUCTION_EDIT,
            'delete' => RouteAdminSystem::INTRODUCTION_DELETE,
        ];
    }

    public function index(IntroductionDataTable $dataTable)
    {
        $actionMultiple = [
            1 => 'Xuất bản',
            2 => 'Bản nháp / Ẩn',
        ];

        return $dataTable->render($this->view['index'], [
            'actionMultiple' => $actionMultiple,
            'breadcrumbs' => $this->crums->add(__('Giới thiệu nền tảng')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'section_types' => IntroductionSectionType::asSelectArray(),
            'status' => [
                DefaultStatus::Published->value => 'Xuất bản',
                DefaultStatus::Draft->value => 'Bản nháp / Ẩn',
            ],
            'breadcrumbs' => $this->crums->add(__('Giới thiệu nền tảng'), route($this->route['index']))->add(__('Thêm mới')),
        ]);
    }

    public function store(IntroductionRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($req) {
            return $this->service->store($req);
        }, $this->route['index'], $this->route['edit']);
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFail($id);

        return view($this->view['edit'], [
            'instance' => $instance,
            'section_types' => IntroductionSectionType::asSelectArray(),
            'status' => [
                DefaultStatus::Published->value => 'Xuất bản',
                DefaultStatus::Draft->value => 'Bản nháp / Ẩn',
            ],
            'breadcrumbs' => $this->crums->add(__('Giới thiệu nền tảng'), route($this->route['index']))->add(__('Chỉnh sửa')),
        ]);
    }

    public function update(IntroductionRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($req) {
            return $this->service->update($req);
        });
    }

    public function delete($id): RedirectResponse
    {
        $this->service->delete($id);
        return to_route($this->route['index'])->with('success', __('notifySuccess'));
    }

    public function actionMultipleRecode(Request $request): RedirectResponse
    {
        $success = $this->service->actionMultipleRecode($request);
        if ($success) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}
