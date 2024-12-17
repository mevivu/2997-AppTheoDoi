<?php

namespace App\Admin\Http\Controllers\Children;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Children\ChildrenRequest;
use App\Admin\Repositories\Children\ChildrenRepositoryInterface;
use App\Admin\Services\Children\ChildrenServiceInterface;
use App\Admin\DataTables\Children\ChildrenDataTable;
use App\Enums\Child\BornStatus;
use App\Traits\ResponseController;
use Exception;
use App\Enums\Child\ChildStatus;
use App\Enums\User\Gender;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChildrenController extends Controller
{
    use ResponseController;

    public function __construct(
        ChildrenRepositoryInterface $repository,
        ChildrenServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.children.index',
            'create' => 'admin.children.create',
            'edit' => 'admin.children.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.children.index',
            'create' => 'admin.children.create',
            'edit' => 'admin.children.edit',
            'delete' => 'admin.children.delete',
        ];
    }

    public function index(ChildrenDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'gender' => Gender::asSelectArray(),
                'status' => ChildStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('children')),
            ]

        );
    }


    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'gender' => Gender::asSelectArray(),
            'status' => ChildStatus::asSelectArray(),
            'born' => BornStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('childrenList'), route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(ChildrenRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
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
                'children' => $instance,
                'gender' => Gender::asSelectArray(),
                'status' => ChildStatus::asSelectArray(),
                'born' => BornStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('childrenList'), route($this->route['index']))->add(__('edit')),
            ],
        );

    }

    public function update(ChildrenRequest $request): RedirectResponse
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
        return $this->handleDeleteResponse($id, function ($id) {
            $response = $this->repository->findOrFail($id);
            return $response->update(['status' => ChildStatus::Deleted->value]);
        });
    }

    protected function getActionMultiple(): array
    {
        return [
            'active' => ChildStatus::Active->description(),
            'draft' => ChildStatus::Draft->description(),
            'deleted' => ChildStatus::Deleted->description()
        ];
    }

    public function actionMultipleRecode(Request $request): RedirectResponse
    {
        $boolean = $this->service->actionMultipleRecode($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}
