<?php

namespace App\Admin\Http\Controllers\Subject;

use App\Admin\DataTables\Subject\SubjectDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Subject\SubjectRequest;
use App\Admin\Repositories\Subject\SubjectRepositoryInterface;
use App\Admin\Services\Subject\SubjectServiceInterface;
use App\Enums\ActiveStatus;
use App\Models\SchoolClass;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    use ResponseController;

    public function __construct(
        SubjectRepositoryInterface $repository,
        SubjectServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.subject.index',
            'create' => 'admin.subject.create',
            'edit' => 'admin.subject.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.subject.index',
            'create' => 'admin.subject.create',
            'edit' => 'admin.subject.edit',
            'delete' => 'admin.subject.delete',
        ];
    }

    public function index(SubjectDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Môn học')),
            ]

        );
    }

    public function create(): Factory|View|Application
    {
        $classes = SchoolClass::where('status', ActiveStatus::Active->value)->pluck('name', 'id');
        return view($this->view['create'], [
            'classes' => $classes,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(
                __('Môn học'),
                route($this->route['index'])
            )->add(__('add')),
        ]);
    }

    public function store(SubjectRequest $request): RedirectResponse
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
        $classes = SchoolClass::where('status', ActiveStatus::Active->value)->pluck('name', 'id');
        $instance = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'classes' => $classes,
                'response' => $instance,
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(
                    __('Môn học'),
                    route($this->route['index'])
                )->add(__('edit')),
            ],
        );

    }

    public function update(SubjectRequest $request): RedirectResponse
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