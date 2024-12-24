<?php

namespace App\Admin\Http\Controllers\Classes;

use App\Admin\DataTables\Classes\ClassesDatable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Classes\ClassesRequest;
use App\Admin\Repositories\Classes\ClassesRepositoryInterface;
use App\Admin\Services\Classes\ClassesServiceInterface;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    use ResponseController;

    public function __construct(
        ClassesRepositoryInterface $repository,
        ClassesServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.classes.index',
            'create' => 'admin.classes.create',
            'edit' => 'admin.classes.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.classes.index',
            'create' => 'admin.classes.create',
            'edit' => 'admin.classes.edit',
            'delete' => 'admin.classes.delete',
        ];
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('DS Lớp')->add('Thêm'),
        ]);
    }

    public function update(ClassesRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {

        $this->repository->delete($id);
        return redirect()->back()->with('success', __('notifySuccess'));

    }

    public function store(ClassesRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    public function edit(int $id): Factory|View|Application
    {
        $response = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'response' => $response,
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Danh sách Lớp ', route($this->route['index']))->add('Cập nhật'),
            ]
        );
    }

    public function index(ClassesDatable $datable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $datable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('DS lớp')),
            ]
        );
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
