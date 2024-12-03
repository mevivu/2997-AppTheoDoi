<?php

namespace App\Admin\Http\Controllers\QuestionGroup;

use App\Admin\DataTables\QuestionGroup\QuestionGroupDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\QuestionGroup\QuestionGroupRequest;
use App\Admin\Repositories\QuestionGroup\QuestionGroupRepositoryInterface;
use App\Admin\Services\QuestionGroup\QuestionGroupServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class QuestionGroupController extends Controller
{
    public function __construct(
        QuestionGroupRepositoryInterface $repository,
        QuestionGroupServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.question-group.index',
            'create' => 'admin.question-group.create',
            'edit' => 'admin.question-group.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.question-group.index',
            'create' => 'admin.question-group.create',
            'edit' => 'admin.question-group.edit',
            'delete' => 'admin.question-group.delete'
        ];
    }

    public function index(QuestionGroupDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Danh sách nhóm câu hỏi'),
            ]
        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'gender' => Gender::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Danh sách nhóm câu hỏi', route($this->route['index']))->add('Thêm mới'),
        ]);
    }

    public function store(QuestionGroupRequest $request): RedirectResponse
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['edit'], $response)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    /**
     * @throws Exception
     */
    public function edit($id): Factory|View|Application
    {
        $response = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'response' => $response,
                'status' => ActiveStatus::asSelectArray(),
                'gender' => Gender::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Danh sách nhóm câu hỏi', route($this->route['index']))->add('Cập nhật'),
            ]
        );

    }

    public function update(QuestionGroupRequest $request): RedirectResponse
    {
        $this->service->update($request);
        return back()->with('success', __('notifySuccess'));

    }

    public function delete($id): RedirectResponse
    {

        $this->repository->delete($id);
        return redirect()->back()->with('success', __('notifySuccess'));

    }

    protected function getActionMultiple(): array
    {
        return [
            ActiveStatus::Active->value => ActiveStatus::Active->description(),
            ActiveStatus::Draft->value => ActiveStatus::Draft->description(),
            ActiveStatus::Deleted->value => ActiveStatus::Deleted->description(),
        ];
    }

    public function actionMultipleRecords(Request $request)
    {
        $boolean = $this->service->actionMultipleRecords($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}