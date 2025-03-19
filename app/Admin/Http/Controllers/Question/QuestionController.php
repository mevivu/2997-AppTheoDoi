<?php

namespace App\Admin\Http\Controllers\Question;

use App\Admin\DataTables\Question\EqQuestionDataTable;
use App\Admin\DataTables\Question\AqQuestionDataTable;
use App\Admin\DataTables\Question\IqQuestionDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Question\QuestionEqAqRequest;
use App\Admin\Http\Requests\Question\QuestionIqRequest;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Admin\Repositories\QuestionGroup\QuestionGroupRepositoryInterface;
use App\Admin\Repositories\Quiz\QuizRepositoryInterface;
use App\Admin\Services\Question\QuestionServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Answser\AnswerType;
use App\Enums\Question\AgeGroup;
use App\Enums\Question\QuestionType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class QuestionController extends Controller
{
    protected QuestionGroupRepositoryInterface $questionGroupRepository;
    protected QuizRepositoryInterface $quizRepository;

    public function __construct(
        QuestionRepositoryInterface      $repository,
        QuestionGroupRepositoryInterface $questionGroupRepository,
        QuestionServiceInterface         $service,
        QuizRepositoryInterface         $quizRepository
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->questionGroupRepository = $questionGroupRepository;
        $this->service = $service;
        $this->quizRepository = $quizRepository;
    }

    public function getView(): array
    {
        return [
            'iq' => 'admin.question.iq',
            'eq' => 'admin.question.eq',
            'aq' => 'admin.question.aq',

            'createIq' => 'admin.question.create.iq',
            'createEq' => 'admin.question.create.eq-aq',
            'createAq' => 'admin.question.create.eq-aq',

            'editIq' => 'admin.question.edit.iq',
            'editEqAq' => 'admin.question.edit.eq-aq',
        ];
    }

    public function getRoute(): array
    {
        return [
            'iq' => 'admin.question.iq',
            'eq' => 'admin.question.eq',
            'aq' => 'admin.question.aq',
            'createIq' => 'admin.question.createIq',
            'createEq' => 'admin.question.createEq',
            'createAq' => 'admin.question.createAq',
            'editIq' => 'admin.question.editIq',
            'editEqAq' => 'admin.question.editEqAq',
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

    public function eq(EqQuestionDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['eq'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Danh sách câu hỏi EQ'),
            ]
        );
    }

    public function aq(AqQuestionDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['aq'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Danh sách câu hỏi AQ'),
            ]
        );
    }

    public function createIq()
    {
        return view($this->view['createIq'], [
            'answer_types' => AnswerType::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'types' => QuestionType::asSelectArray(),
            'questionGroups' => $this->questionGroupRepository->getByQueryBuilder(['status' => ActiveStatus::Active])->pluck('name', 'id'),
            'breadcrumbs' => $this->crums->add('Danh sách câu hỏi', route($this->route['iq']))->add('Thêm mới'),
            'back' => route('admin.question.iq'),
        ]);
    }

    public function storeIq(QuestionIQRequest $request): RedirectResponse
    {

        $response = $this->service->storeIq($request);
        if ($response) {
            return to_route($this->route['editIq'], $response->id)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    public function editIq($id)
    {
        $response = $this->repository->find($id);
        $type = $response->answers->first()->type->value;
        return view($this->view['editIq'], [
            'response' => $response,
            'type' => $type,
            'answer_types' => AnswerType::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'types' => QuestionType::asSelectArray(),
            'questionGroups' => $this->questionGroupRepository->getByQueryBuilder(['status' => ActiveStatus::Active])->pluck('name', 'id'),
            'breadcrumbs' => $this->crums->add('Danh sách câu hỏi IQ', route($this->route['iq']))->add('Cập nhật'),
            'back' => route('admin.question.iq'),
        ]);
    }

    public function updateIq(QuestionIqRequest $request): RedirectResponse
    {
        if($request->question['status']==ActiveStatus::Deleted->value){
            $this->repository->delete($request->question['id']);
            return to_route($this->route['iq'])->with('success', __('notifySuccess'));
        }else
            $this->service->updateIq($request);
        return back()->with('success', __('notifySuccess'));

    }

    public function createEq()
    {
        return view($this->view['createEq'], [
            'answer_types' => AnswerType::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'types' => QuestionType::asSelectArray(),
            'age_group' => AgeGroup::asSelectArray(),
            'questionGroups' => $this->questionGroupRepository->getByQueryBuilder(['status' => ActiveStatus::Active])->pluck('name', 'id'),
            'breadcrumbs' => $this->crums->add('Danh sách câu hỏi EQ', route($this->route['eq']))->add('Thêm mới'),
            'back' => route('admin.question.eq'),
        ]);
    }

    public function createAq()
    {
        return view($this->view['createAq'], [
            'answer_types' => AnswerType::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'types' => QuestionType::asSelectArray(),
            'age_group' => AgeGroup::asSelectArray(),
            'questionGroups' => $this->questionGroupRepository->getByQueryBuilder(['status' => ActiveStatus::Active])->pluck('name', 'id'),
            'breadcrumbs' => $this->crums->add('Danh sách câu hỏi AQ', route($this->route['aq']))->add('Thêm mới'),
            'back' => route('admin.question.aq'),
        ]);
    }

    public function storeEqAq(QuestionEqAqRequest $request): RedirectResponse
    {
        $response = $this->service->storeEqAq($request);
        if ($response) {
            return to_route($this->route['editEqAq'], $response->id)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    public function editEqAq($id)
    {
        $response = $this->repository->find($id);
        $type = $response->answers->first()->type->value;
        $back = $response->question_type == QuestionType::EQ ? route('admin.question.eq'):route('admin.question.aq');
        return view($this->view['editEqAq'], [
            'response' => $response,
            'type' => $type,
            'answer_types' => AnswerType::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'types' => QuestionType::asSelectArray(),
            'age_group' => AgeGroup::asSelectArray(),
            'questionGroups' => $this->questionGroupRepository->getByQueryBuilder(['status' => ActiveStatus::Active])->pluck('name', 'id'),
            'breadcrumbs' => $this->crums->add('Danh sách câu hỏi', route($this->route['eq']))->add('Cập nhật'),
            'back' => $back,
        ]);
    }

    public function updateEqAq(QuestionEqAqRequest $request): RedirectResponse
    {
        if($request->question['status']==ActiveStatus::Deleted->value){
            $this->repository->delete($request->question['id']);
            if($request->question['question_type']==QuestionType::EQ->value)
                return to_route($this->route['eq'])->with('success', __('notifySuccess'));
            return to_route($this->route['aq'])->with('success', __('notifySuccess'));
        }else
            $this->service->updateEqAq($request);
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

    public function actionMultipleRecords(Request $request): RedirectResponse
    {
        $boolean = $this->service->actionMultipleRecords($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    public function getQuestionsByType(Request $request): JsonResponse
    {
        try {
            $type = $request['type'];
            $keyword = $request->get('keyword', '');
            $filterAge = $request->get('age_group', '');
            $query = $this->repository->getByQueryBuilder([
                'question_type' => $type,
                'status' => ActiveStatus::Active
            ]);
            if (!empty($filterAge)) {
                $query->where('age_group', $filterAge);
            }
            if (!empty($keyword)) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('question', 'like', '%' . $keyword . '%');

                });
            }
            $questions = $query->orderBy('created_at', 'desc')->take(20)->get();
            $questions->load('group');

            return response()->json(['data' => $questions], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function getQuestionsByIds(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json(['data' => [], 'message' => 'No IDs provided'], 400);
            }
            $quizId = $request['quiz_id'];
            $load = filter_var($request->get('load'), FILTER_VALIDATE_BOOLEAN);

            if($load && $quizId){
                $quiz = $this->quizRepository->findOrFail($quizId);
                $questions = $quiz->questions;
                if (!empty($ids)) {
                    $additionalVideos = $this->repository->getByQueryBuilder([
                        ['id', 'IN', $ids],
                        'question_type' => $quiz->type,
                        'status' => ActiveStatus::Active
                    ])->get();

                    $questionsData = $questions->merge($additionalVideos);
                    $questionsData->load('group');
                }
            }
            else {
                $questions = $this->repository->getByQueryBuilder([
                    ['id', 'IN', $ids],
                    'status' => ActiveStatus::Active
                ])->get();
            }


            return response()->json(['data' => $questions], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

}
