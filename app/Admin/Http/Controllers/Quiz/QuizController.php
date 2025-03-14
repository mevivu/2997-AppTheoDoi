<?php

namespace App\Admin\Http\Controllers\Quiz;

use App\Admin\DataTables\Quiz\QuizDataTable;
use App\Admin\DataTables\Quiz\QuizIQDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Quiz\QuizRequest;
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
            'edit' => 'admin.quiz.edit',
            'edit-iq' => 'admin.quiz.edit-iq',
            'create-eq-aq' => 'admin.quiz.create-eq-aq',
        ];
    }

    public function getRoute(): array
    {
        return [
            'iq' => 'admin.quiz.iq',
            'eq' => 'admin.quiz.eq',
            'aq' => 'admin.quiz.aq',
            'pq' => 'admin.quiz.pq',
            'createIq' => 'admin.quiz.createIq',
            'createEq' => 'admin.quiz.createEq',
            'createAq' => 'admin.quiz.createAq',
            'createPq' => 'admin.quiz.createPq',
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

    public function eq(QuizDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'age_group' => AgeGroup::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Bài kiểm tra EQ')),
                'title' => __('Bài kiểm tra EQ'),
                'route' => $this->route['createEq'],
            ]

        );
    }

    public function aq(QuizDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'age_group' => AgeGroup::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Bài kiểm tra AQ')),
                'title' => __('Bài kiểm tra AQ'),
                'route' => $this->route['createAq'],
            ]

        );
    }

    public function pq(QuizDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Bài kiểm tra PQ')),
                'title' => __('Bài kiểm tra PQ'),
                'route' => $this->route['createPq']
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

    public function createEq(): Factory|View|Application|RedirectResponse
    {
        $types = [QuestionType::EQ->value];
        if ($this->service->checkTypeExists($types)) {
            return redirect()->route($this->route['eq'])
                ->with('error', __('Bài kiểm tra EQ đã tồn tại!.'));
        }
        $breadcrumbs = $this->crums->add(__('Bài kiểm tra EQ'), route($this->route['eq']));
        return view($this->view['create-eq-aq'], [
            'status' => ActiveStatus::asSelectArray(),
            'type' => QuestionType::asSelectArray(),
            'age_group' => AgeGroup::asSelectArray(),
            'breadcrumbs' => $breadcrumbs->add(__('add')),
            'title' => __('Bài kiểm tra EQ'),
            'route' => $this->route['eq'],
            'selectedType' => QuestionType::EQ->value,
        ]);
    }

    public function createAq(): Factory|View|Application|RedirectResponse
    {
        $types = [QuestionType::AQ->value];
        if ($this->service->checkTypeExists($types)) {
            return redirect()->route($this->route['aq'])
                ->with('error', __('Bài kiểm tra AQ đã tồn tại!.'));
        }
        $breadcrumbs = $this->crums->add(__('Bài kiểm tra AQ'), route($this->route['aq']));
        return view($this->view['create-eq-aq'], [
            'status' => ActiveStatus::asSelectArray(),
            'type' => QuestionType::asSelectArray(),
            'age_group' => AgeGroup::asSelectArray(),
            'breadcrumbs' => $breadcrumbs->add(__('add')),
            'title' => __('Bài kiểm tra AQ'),
            'route' => $this->route['aq'],
            'selectedType' => QuestionType::AQ->value,
        ]);
    }

    public function createPq(): Factory|View|Application
    {
        $breadcrumbs = $this->crums->add(__('Bài kiểm tra PQ'), route($this->route['pq']));
        return view($this->view['create-eq-aq'], [
            'status' => ActiveStatus::asSelectArray(),
            'type' => QuestionType::asSelectArray(),
            'age_group' => AgeGroup::asSelectArray(),
            'breadcrumbs' => $breadcrumbs->add(__('add')),
            'title' => __('Bài kiểm tra PQ'),
            'route' => $this->route['pq'],
            'selectedType' => QuestionType::PQ->value,
        ]);
    }

    public function store(QuizRequest $request): RedirectResponse
    {
        $response = $this->service->store($request);
        if ($response) {
            switch ($response->type->value) {
                case QuestionType::IQ->value:
                    return redirect()->route($this->route['iq'])
                        ->with('success', __('notifySuccess'));
                case QuestionType::EQ->value:
                    return redirect()->route($this->route['eq'])
                        ->with('success', __('notifySuccess'));
                case QuestionType::AQ->value:
                    return redirect()->route($this->route['aq'])
                        ->with('success', __('notifySuccess'));
                case QuestionType::PQ->value:
                    return redirect()->route($this->route['pq'])
                        ->with('success', __('notifySuccess'));
                default:
                    return redirect()->route($this->route['iq'])
                        ->with('success', __('notifySuccess'));
            }
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

        if ($instance->type->value == QuestionType::IQ->value) {
            $breadcrumbs = $this->crums->add(__('Bài kiểm tra IQ'), route($this->route['iq']));
            $route = $this->route['iq'];
        } elseif ($instance->type->value == QuestionType::EQ->value) {
            $breadcrumbs = $this->crums->add(__('Bài kiểm tra EQ'), route($this->route['eq']));
            $route = $this->route['eq'];
        } elseif ($instance->type->value == QuestionType::AQ->value) {
            $breadcrumbs = $this->crums->add(__('Bài kiểm tra AQ'), route($this->route['aq']));
            $route = $this->route['aq'];
        } elseif ($instance->type->value == QuestionType::PQ->value) {
            $breadcrumbs = $this->crums->add(__('Bài kiểm tra PQ'), route($this->route['pq']));
            $route = $this->route['pq'];
        } else {
            $breadcrumbs = $this->crums->add(__('Bài kiểm tra IQ'), route($this->route['iq']));
            $route = $this->route['iq'];
        }

        $view = $instance->type == QuestionType::IQ ? $this->view['edit-iq'] : $this->view['edit'];

        return view(
            $view,
            [
                'instance' => $instance,
                'status' => ActiveStatus::asSelectArray(),
                'age_group' => AgeGroup::asSelectArray(),
                'type' => QuestionType::asSelectArray(),
                'selected_questions' => $selectedQuestions,
                'questions_type' => $questionsType,
                'breadcrumbs' => $breadcrumbs->add(__('edit')),
                'route' => $route,
            ],
        );

    }

    /**
     * @throws Exception
     */
    public function update(QuizRequest $request): RedirectResponse
    {
        $response = $this->service->update($request);
        return redirect()->back()
            ->with($response ? 'success' : 'error', $response ? __('notifySuccess') : __('notifyFail'));
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
