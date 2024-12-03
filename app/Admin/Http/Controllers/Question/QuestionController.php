<?php

namespace App\Admin\Http\Controllers\Question;

use App\Admin\DataTables\Question\IqQuestionDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Question\QuestionRequest;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Admin\Services\Question\QuestionServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;
use App\Enums\User\Gender;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class QuestionController extends Controller
{
    public function __construct(
        QuestionRepositoryInterface $repository,
        QuestionServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'iq' => 'admin.question.iq',
            'eq' => 'admin.question.eq',
            'aq' => 'admin.question.aq',
            'create' => 'admin.question.create',
            'edit' => 'admin.question.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'iq' => 'admin.question.iq',
            'eq' => 'admin.question.eq',
            'aq' => 'admin.question.aq',
            'create' => 'admin.question.create',
            'edit' => 'admin.question.edit',
            'delete' => 'admin.question.delete'
        ];
    }

    public function iq(IqQuestionDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['iq'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Danh sách câu hỏi IQ'),
            ]
        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'types' => QuestionType::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Danh sách câu hỏi', route($this->route['iq']))->add('Thêm mới'),
        ]);
    }

    public function store(QuestionRequest $request): RedirectResponse
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
                'types' => QuestionType::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Danh sách câu hỏi', route($this->route['iq']))->add('Cập nhật'),
            ]
        );

    }

    public function update(QuestionRequest $request): RedirectResponse
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