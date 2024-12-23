<?php

namespace App\Admin\Http\Controllers\Support;

use App\Admin\DataTables\Support\GuideDataTable;
use App\Admin\DataTables\Support\HelpCenterDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Support\SupportRequest;
use App\Admin\Repositories\Support\SupportRepositoryInterface;
use App\Admin\Services\Support\SupportServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Support\SupportType;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    use ResponseController;

    public function __construct(
        SupportRepositoryInterface $repository,
        SupportServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'help-center' => 'admin.support.help-center',
            'guide' => 'admin.support.guide',
            'create' => 'admin.support.create',
            'edit' => 'admin.support.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'help-center' => 'admin.support.help-center',
            'guide' => 'admin.support.guide',
            'create' => 'admin.support.create',
            'edit' => 'admin.support.edit',
            'delete' => 'admin.support.delete',
        ];
    }

    public function helpCenter(HelpCenterDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['help-center'],
            [
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Trung tâm trợ giúp')),
            ]

        );
    }

    public function guide(GuideDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['guide'],
            [
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Hướng dẫn sử dụng')),
            ]

        );
    }


    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'type' => SupportType::asSelectArray(),
            'breadcrumbs' => $this->crums->add(
                __('Hỗ trợ khách hàng'),
                route($this->route['help-center'])
            )->add(__('add')),
        ]);
    }

    public function store(SupportRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['help-center'], $this->route['edit']);
    }

    /**
     * @throws Exception
     */
    public function edit($id): Factory|View|Application
    {

        $instance = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'response' => $instance,
                'type' => SupportType::asSelectArray(),
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(
                    __($instance->type->value == SupportType::HelpCenter->value ? 'Trung tâm trợ giúp' : 'Hướng dẫn sử dụng'),
                    route($instance->type->value == SupportType::HelpCenter->value ? $this->route['help-center'] : $this->route['guide'])
                )->add(__('edit')),
            ],
        );

    }

    public function update(SupportRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    /**
     * @throws Exception
     */
    public function delete($id): RedirectResponse
    {
        try {
            $this->repository->delete($id);
            return redirect()->back()->with('success', __('notifySuccess'));
        } catch (Exception $exception) {
            return redirect()->back()->with('error', __('notifyFail'));
        }
    }

    protected function getActionMultiple(): array
    {
        return [
            'active' => ActiveStatus::Active->description(),
            'draft' => ActiveStatus::Draft->description(),
            'deleted' => ActiveStatus::Deleted->description()
        ];
    }

    public function actionMultipleRecords(Request $request): RedirectResponse
    {
        $boolean = $this->service->actionMultipleRecords($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}