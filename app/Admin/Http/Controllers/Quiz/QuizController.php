<?php

namespace App\Admin\Http\Controllers\Quiz;

use App\Admin\DataTables\Quiz\QuizIQDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Quiz\IQ\QuizIQRequest;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Admin\Repositories\Quiz\QuizRepositoryInterface;
use App\Admin\Services\Quiz\QuizServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Question\AgeGroup;
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
            'edit-iq' => 'admin.quiz.edit-iq',
        ];
    }

    public function getRoute(): array
    {
        return [
            'iq' => 'admin.quiz.iq',
            'createIq' => 'admin.quiz.createIq',
            'edit' => 'admin.quiz.edit',
            'delete' => 'admin.quiz.delete',
        ];
    }

    public function iq(QuizIQDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Bài kiểm tra IQ')),
                'title' => __('Bài kiểm tra IQ'),
                'route' => $this->route['createIq'],
            ]
        );
    }

    public function createIq(): Factory|View|Application
    {
        $breadcrumbs = $this->crums->add(__('Bài kiểm tra IQ'), route($this->route['iq']));
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'type' => QuestionType::asSelectArray(),
            'breadcrumbs' => $breadcrumbs->add(__('add')),
            'title' => __('Bài kiểm tra IQ'),
            'route' => $this->route['iq'],
            'selectedType' => QuestionType::IQ->value,
        ]);
    }

    public function storeIQ(QuizIQRequest $request): RedirectResponse
    {
        $response = $this->service->storeIQ($request);
        if ($response && $response->type == QuestionType::IQ) {
            return redirect()->route($this->route['iq'])
                ->with('success', __('notifySuccess'));
        } else {
            return redirect()->back()
                ->with('error', __('notifyFail'));
        }
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
        $mergedQuestions = $questionsType->filter(function ($question) use ($selectedQuestions) {
            return !$selectedQuestions->contains('id', $question->id);
        });
        $mergedQuestions = $mergedQuestions->merge($selectedQuestions);

        $breadcrumbs = $this->crums->add(__('Bài kiểm tra IQ'), route($this->route['iq']));
        $route = $this->route['iq'];

        $statusOptions = ActiveStatus::asSelectArray();
        unset($statusOptions[ActiveStatus::Deleted->value]);
        return view(
            $this->view['edit-iq'],
            [
                'instance' => $instance,
                'status' => $statusOptions,
                'age_group' => AgeGroup::asSelectArray(),
                'type' => QuestionType::asSelectArray(),
                'selected_questions' => $selectedQuestions,
                'questions_type' => $mergedQuestions,
                'breadcrumbs' => $breadcrumbs->add(__('edit')),
                'route' => $route,
                'selectedType' => $instance->type->value,
            ],
        );
    }

    public function updateIQ(QuizIQRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->updateIQ($request);
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
