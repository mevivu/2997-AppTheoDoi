<?php

namespace App\Admin\Http\Controllers\Quiz;

use App\Admin\DataTables\Quiz\QuizDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Quiz\QuizRequest;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Admin\Repositories\Quiz\QuizRepositoryInterface;
use App\Admin\Services\Quiz\QuizServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    use ResponseController;

    protected QuestionRepositoryInterface $questionRepository;


    public function __construct(
        QuizRepositoryInterface     $repository,
        QuestionRepositoryInterface $questionRepository,
        QuizServiceInterface        $service
    )
    {

        parent::__construct();

        $this->repository = $repository;
        $this->questionRepository = $questionRepository;
        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.quiz.index',
            'create' => 'admin.quiz.create',
            'edit' => 'admin.quiz.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.quiz.index',
            'create' => 'admin.quiz.create',
            'edit' => 'admin.quiz.edit',
            'delete' => 'admin.quiz.delete',
        ];
    }

    public function index(QuizDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('quiz')),
            ]

        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'type' => QuestionType::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('quiz'),
                route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(QuizRequest $request): RedirectResponse
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
        $selectedQuestions = $instance->questions;
        $questionsType = $this->questionRepository->getBy([
            'status' => ActiveStatus::Active,
            'question_type' => $instance->type,
        ]);
        return view(
            $this->view['edit'],
            [
                'instance' => $instance,
                'status' => ActiveStatus::asSelectArray(),
                'type' => QuestionType::asSelectArray(),
                'selected_questions' => $selectedQuestions,
                'questions_type' => $questionsType,
                'breadcrumbs' => $this->crums->add(__('quiz'), route($this->route['index']))->add(__('edit')),
            ],
        );

    }

    /**
     * @throws Exception
     */
    public function update(QuizRequest $request): RedirectResponse
    {
        if ($request['status'] == ActiveStatus::Deleted->value) {
            $this->repository->delete($request['id']);
            return redirect()->route($this->route['index'])
                ->with('success', __('notifySuccess'));
        }
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
            return $response->update(['status' => ActiveStatus::Deleted->value]);
        });
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
