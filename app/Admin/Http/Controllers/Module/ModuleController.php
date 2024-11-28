<?php

namespace App\Admin\Http\Controllers\Module;

use App\Admin\DataTables\Module\ModuleData1Table;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Module\ModuleRequest;
use App\Admin\Repositories\Module\ModuleRepositoryInterface;
use App\Admin\Services\Module\ModuleServiceInterface;
use App\Admin\DataTables\Module\ModuleDataTable;
use App\Enums\Module\ModuleStatus;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class ModuleController extends Controller
{
    public function __construct(
        ModuleRepositoryInterface $repository,
        ModuleServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;


        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.module.index',
            'summary' => 'admin.module.summary',
            'create' => 'admin.module.create',
            'edit' => 'admin.module.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.module.index',
            'summary' => 'admin.module.summary',
            'create' => 'admin.module.create',
            'edit' => 'admin.module.edit',
            'delete' => 'admin.module.delete'
        ];
    }

    public function index(ModuleDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'actionMultiple' => [
                'delete' => "Xoá mục đã chọn"
            ]
        ]);
    }


    public function summary(): Factory|View|Application
    {
        $listmodules = $this->repository->getAllModulesWithPermissions();
        return view($this->view['summary'], [
            'listmodules' => $listmodules
        ]);
    }


    public function create(): Factory|View|Application
    {
        $listpermissions = $this->repository->getAllPermissions();
        return view($this->view['create'], [
            'listpermissions' => $listpermissions,
            'status' => ModuleStatus::asSelectArray()
        ]);
    }

    public function store(ModuleRequest $request): RedirectResponse
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['edit'], $response)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'))->withInput();
    }

    /**
     * @throws Exception
     */
    public function edit($id): Factory|View|Application
    {
        $response = $this->repository->findOrFail($id);
        $listpermissions = $this->repository->getAllPermissionsOfTheModule($id);
        return view(
            $this->view['edit'],
            [
                'module' => $response,
                'listpermissions' => $listpermissions,
                'status' => ModuleStatus::asSelectArray()
            ]
        );

    }

    public function update(ModuleRequest $request): RedirectResponse
    {

        $this->service->update($request);

        return back()->with('success', __('notifySuccess'));

    }

    public function delete($id): RedirectResponse
    {

        $this->service->delete($id);

        return to_route($this->route['index'])->with('success', __('notifySuccess'));

    }

    public function actionMultipleRecord(Request $request)
    {
        $boolean = $this->service->actionMultipleRecord($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}
