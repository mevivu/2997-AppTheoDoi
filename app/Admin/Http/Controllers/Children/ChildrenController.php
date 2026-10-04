<?php

namespace App\Admin\Http\Controllers\Children;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Children\ChildrenRequest;
use App\Admin\Repositories\Children\ChildrenRepositoryInterface;
use App\Admin\Services\Children\ChildrenServiceInterface;
use App\Admin\DataTables\Children\ChildrenDataTable;
use App\Api\V1\Services\ChildEvaluation\ChildEvaluationServiceInterface;
use App\Api\V1\Services\HeightPrediction\HeightPredictionServiceInterface;
use App\Api\V1\Services\RatingPQ\RatingPQServiceInterface;
use App\Api\V2\Http\Requests\HeightPrediction\HeightChartV2Request;
use App\Api\V2\Http\Requests\HeightPrediction\HeightPredictionV2Request;
use App\Enums\Child\BornStatus;
use App\Models\ClassGrade;
use App\Services\ReportCard\ReportCardEngine;
use App\Services\ReportCard\ReportCardInputLoader;
use App\Services\ReportCard\ReportCardPersister;
use App\Services\ReportCard\ReportCardSummaryService;
use App\Traits\ResponseController;
use Exception;
use App\Enums\Child\ChildStatus;
use App\Enums\ActiveStatus;
use App\Models\FetalGrowthStandard;
use Carbon\Carbon;
use App\Enums\User\Gender;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Throwable;

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

        $heightPrediction = null;
        $heightChart = null;
        $pqOverall = null;

        // Chỉ tính toán dự báo chiều cao và PQ nếu trẻ đã sinh và có ngày sinh hợp lệ
        if ($instance->is_born != BornStatus::Unborn && $instance->birthday) {
            try {
                $heightService = app(HeightPredictionServiceInterface::class);

                $heightRequest = new HeightPredictionV2Request();
                $heightRequest->setValidator(validator([
                    'child_id' => $id,
                    'puberty_months' => 0
                ], [
                    'child_id' => 'required|numeric',
                    'puberty_months' => 'required|numeric|min:0|max:96'
                ]));
                $heightPrediction = $heightService->indexV2($heightRequest);

                $chartRequest = new HeightChartV2Request();
                $chartRequest->setValidator(validator([
                    'child_id' => $id,
                    'puberty_months' => 0,
                    'target_height' => null
                ], [
                    'child_id' => 'required|numeric',
                    'puberty_months' => 'required|numeric|min:0|max:96',
                    'target_height' => 'nullable|numeric|min:50|max:250'
                ]));
                $heightChart = $heightService->chartV2($chartRequest);
            } catch (Throwable $e) {
                Log::warning('Error calculating height prediction/chart V2 for child ' . $id . ': ' . $e->getMessage());
            }

            try {
                $pqService = app(RatingPQServiceInterface::class);
                $pqOverall = $pqService->getOverallStats(new Request(['child_id' => $id]), (int)$id);
            } catch (Throwable $e) {
                Log::warning('Error calculating PQ overall for child ' . $id . ': ' . $e->getMessage());
            }
        }

        $pregnancyOverview = null;
        if ($instance->is_born == BornStatus::Unborn || $instance->due_date) {
            try {
                $dueDate = $instance->due_date ? Carbon::parse($instance->due_date)->startOfDay() : null;
                if ($dueDate) {
                    $today = Carbon::now()->startOfDay();
                    $daysRemaining = (int) $today->diffInDays($dueDate, false);
                    $gestationalAgeDays = 280 - $daysRemaining;
                    $currentWeek = $gestationalAgeDays > 0 ? intdiv($gestationalAgeDays, 7) : 0;
                    $extraDays = $gestationalAgeDays > 0 ? ($gestationalAgeDays % 7) : 0;

                    $lookupWeek = max(1, min(50, $currentWeek > 0 ? $currentWeek : 8));
                    $standard = FetalGrowthStandard::where('status', ActiveStatus::Active->value)
                        ->where('week', $lookupWeek)
                        ->first();
                    if (!$standard) {
                        $standard = FetalGrowthStandard::where('status', ActiveStatus::Active->value)
                            ->orderByRaw("ABS(week - {$lookupWeek}) ASC")
                            ->first();
                    }

                    $progressPercent = round(min(max(($gestationalAgeDays / 280) * 100, 0), 100), 1);

                    $pregnancyOverview = [
                        'dueDate' => $dueDate->format('d/m/Y'),
                        'currentWeek' => $currentWeek,
                        'extraDays' => $extraDays,
                        'weekDisplay' => "Tuần {$currentWeek}" . ($extraDays > 0 ? " + {$extraDays} ngày" : ""),
                        'daysRemaining' => $daysRemaining,
                        'isDeliveredOrDue' => $daysRemaining <= 0,
                        'progressPercent' => $progressPercent,
                        'standard' => [
                            'week' => (int) ($standard?->week ?? $lookupWeek),
                            'length' => $standard?->length ? (float)$standard->length : null,
                            'weight' => $standard?->weight ? (float)$standard->weight : null,
                            'head_circumference' => $standard?->head_circumference ? (float)$standard->head_circumference : null,
                            'description' => $standard?->description ?? null,
                        ],
                        'debug' => [
                            'child_id' => $instance->id,
                            'child_name' => $instance->fullname,
                            'is_born' => is_object($instance->is_born) ? $instance->is_born->value : $instance->is_born,
                            'raw_due_date' => $instance->due_date,
                            'parsed_due_date' => $dueDate->format('d/m/Y'),
                            'parsed_due_date_iso' => $dueDate->format('Y-m-d'),
                            'system_today' => $today->format('d/m/Y'),
                            'system_today_iso' => $today->format('Y-m-d'),
                            'days_remaining' => $daysRemaining,
                            'formula_gestational_age' => "280 - {$daysRemaining} = {$gestationalAgeDays} ngày",
                            'gestational_age_days' => $gestationalAgeDays,
                            'formula_week' => "intdiv({$gestationalAgeDays}, 7) = {$currentWeek} tuần",
                            'formula_extra_days' => "{$gestationalAgeDays} % 7 = {$extraDays} ngày",
                            'current_week' => $currentWeek,
                            'extra_days' => $extraDays,
                            'progress_percent' => $progressPercent,
                            'lookup_week' => $lookupWeek,
                            'sql_query' => "SELECT * FROM fetal_growth_standards WHERE status = " . ActiveStatus::Active->value . " AND week = {$lookupWeek} LIMIT 1",
                            'matched_standard_id' => $standard?->id,
                            'standard_record' => $standard ? [
                                'id' => $standard->id,
                                'week' => $standard->week,
                                'length' => $standard->length ? (float)$standard->length : null,
                                'weight' => $standard->weight ? (float)$standard->weight : null,
                                'head_circumference' => $standard->head_circumference ? (float)$standard->head_circumference : null,
                                'description' => $standard->description,
                                'status' => $standard->status,
                            ] : null,
                        ],
                    ];
                }
            } catch (Throwable $e) {
                Log::warning('Error calculating pregnancy overview for child ' . $id . ': ' . $e->getMessage());
            }
        }


        $reportCardSummary = null;
        try {
            $summaryService = app(ReportCardSummaryService::class);
            $reportCardSummary = $summaryService->getSummary((int) $id);
        } catch (Throwable $e) {
            Log::warning('Error getting report card summary for child ' . $id . ': ' . $e->getMessage());
        }

        return view(
            $this->view['edit'],
            [
                'children' => $instance,
                'pregnancyOverview' => $pregnancyOverview,
                'heightPrediction' => $heightPrediction,
                'heightChart' => $heightChart,
                'pqOverall' => $pqOverall,
                'reportCardSummary' => $reportCardSummary,
                'gender' => Gender::asSelectArray(),
                'birthday' => $instance->birthday,
                'dueDate' => $instance->due_date,
                'status' => ChildStatus::asSelectArray(),
                'born' => BornStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('childrenList'), route($this->route['index']))->add(__('edit')),
            ],
        );
    }

    public function ajaxPredictHeightV2(Request $request): JsonResponse
    {
        try {
            $childId = $request->input('child_id');
            $pubertyMonths = (float)$request->input('puberty_months', 0);

            $validator = validator([
                'child_id' => $childId,
                'puberty_months' => $pubertyMonths,
            ], [
                'child_id' => 'required|numeric',
                'puberty_months' => 'required|numeric|min:0|max:96',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $heightService = app(HeightPredictionServiceInterface::class);
            $serviceRequest = new HeightPredictionV2Request();
            $serviceRequest->setValidator($validator);

            $result = $heightService->indexV2($serviceRequest);

            return response()->json([
                'status' => 200,
                'message' => 'Tính toán dự báo chiều cao V2 thành công.',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            Log::error('ajaxPredictHeightV2 error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi tính toán dự báo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ajaxHeightChartV2(Request $request): JsonResponse
    {
        try {
            $childId = $request->input('child_id');
            $pubertyMonths = (float)$request->input('puberty_months', 0);
            $targetHeight = $request->filled('target_height') ? (float)$request->input('target_height') : null;

            $validator = validator([
                'child_id' => $childId,
                'puberty_months' => $pubertyMonths,
                'target_height' => $targetHeight,
            ], [
                'child_id' => 'required|numeric',
                'puberty_months' => 'required|numeric|min:0|max:96',
                'target_height' => 'nullable|numeric|min:50|max:250',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $heightService = app(HeightPredictionServiceInterface::class);
            $serviceRequest = new HeightChartV2Request();
            $serviceRequest->setValidator($validator);

            $result = $heightService->chartV2($serviceRequest);

            return response()->json([
                'status' => 200,
                'message' => 'Lấy dữ liệu phác đồ chiều cao V2 thành công.',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            Log::error('ajaxHeightChartV2 error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi tải phác đồ: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ajaxDebugHeightV2(Request $request): JsonResponse
    {
        try {
            $childId = $request->input('child_id');
            $pubertyMonths = (float)$request->input('puberty_months', 0);
            $targetHeight = $request->filled('target_height') ? (float)$request->input('target_height') : null;

            $validator = validator([
                'child_id' => $childId,
                'puberty_months' => $pubertyMonths,
                'target_height' => $targetHeight,
            ], [
                'child_id' => 'required|numeric',
                'puberty_months' => 'required|numeric|min:0|max:96',
                'target_height' => 'nullable|numeric|min:50|max:250',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $heightService = app(HeightPredictionServiceInterface::class);
            $debugData = $heightService->debugHeightRegimen((int)$childId, $pubertyMonths, $targetHeight);

            return response()->json([
                'status' => 200,
                'message' => 'Lấy dữ liệu chẩn đoán công thức thành công.',
                'data' => $debugData,
            ]);
        } catch (Exception $e) {
            Log::error('ajaxDebugHeightV2 error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy dữ liệu chẩn đoán: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ajaxDebugPQ(Request $request): JsonResponse
    {
        try {
            $childId = $request->input('child_id');

            $validator = validator([
                'child_id' => $childId,
            ], [
                'child_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $pqService = app(RatingPQServiceInterface::class);
            $debugData = $pqService->debugPqCalculation((int)$childId);

            return response()->json([
                'status' => 200,
                'message' => 'Lấy dữ liệu chẩn đoán thể chất PQ thành công.',
                'data' => $debugData,
            ]);
        } catch (Exception $e) {
            Log::error('ajaxDebugPQ error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy dữ liệu chẩn đoán thể chất PQ: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ajaxDebugReportCard(Request $request): JsonResponse
    {
        try {
            $childId = $request->input('child_id');
            $classId = $request->input('class_id');
            $semester = $request->input('semester');

            $validator = validator([
                'child_id' => $childId,
                'class_id' => $classId,
                'semester' => $semester,
            ], [
                'child_id' => 'required|numeric',
                'class_id' => 'required|numeric',
                'semester' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $classGrade = ClassGrade::where('child_id', $childId)
                ->where('class_id', $classId)
                ->first();

            if (!$classGrade) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Chưa có bảng điểm lớp của trẻ cho năm học này.',
                ], 404);
            }

            $evaluation = $classGrade->evaluations()->where('semester', $semester)->first();

            $loader = app(ReportCardInputLoader::class);
            $engine = app(ReportCardEngine::class);

            $input = $loader->load($classGrade, $semester);
            $calcResult = $engine->calculate($input);

            $className = $classGrade->class?->name ?? "Lớp {$classId}";

            $subjectsData = [];
            foreach ($calcResult->subjects as $sr) {
                $subModel = $classGrade->class?->subjects?->firstWhere('id', $sr->subjectId);
                $isComment = ($sr->method === 'comment');
                $subjectsData[] = [
                    'subject_id' => $sr->subjectId,
                    'name' => $sr->name ?: ($subModel?->name ?? "Môn #{$sr->subjectId}"),
                    'method' => $sr->method,
                    'grade' => !$isComment ? $sr->value : null,
                    'achievement_level' => $isComment ? (string) $sr->value : null,
                    'full_year_grade' => ($semester === 'full_year' && !$isComment) ? $sr->value : null,
                    'full_year_grade_source' => $sr->source,
                    'hk1' => $sr->hk1,
                    'hk2' => $sr->hk2,
                    'is_passed' => (bool) $sr->passed,
                    'is_required' => (bool) $sr->isRequired,
                ];
            }

            return response()->json([
                'status' => 200,
                'message' => 'Lấy dữ liệu chẩn đoán học bạ thành công.',
                'data' => [
                    'child_id' => (int) $childId,
                    'class_id' => (int) $classId,
                    'class_name' => $className,
                    'education_level' => $calcResult->educationLevel,
                    'regulation' => $calcResult->regulation,
                    'semester' => $semester,
                    'calculation_status' => $calcResult->status,
                    'calculated_academic_performance' => $calcResult->rating,
                    'current_academic_performance' => $evaluation?->academic_performance?->value ?? $evaluation?->academic_performance,
                    'is_performance_overridden' => (bool) $evaluation?->is_performance_overridden,
                    'teacher_remark' => $evaluation?->teacher_remark,
                    'version' => $calcResult->version,
                    'subjects' => $subjectsData,
                    'rules' => $calcResult->rules,
                    'adjustment' => $calcResult->adjustment,
                    'missing' => $calcResult->missing,
                    'warnings' => $calcResult->warnings,
                    'snapshot' => $calcResult->snapshot(),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('ajaxDebugReportCard error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi chẩn đoán dữ liệu học bạ: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ajaxRecalculateReportCard(Request $request): JsonResponse
    {
        try {
            $childId = $request->input('child_id');
            $classId = $request->input('class_id');
            $semester = $request->input('semester');
            $applyFinal = (bool) $request->input('apply_final', false);

            $validator = validator([
                'child_id' => $childId,
                'class_id' => $classId,
                'semester' => $semester,
            ], [
                'child_id' => 'required|numeric',
                'class_id' => 'required|numeric',
                'semester' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            $service = app(ChildEvaluationServiceInterface::class);
            $service->findAndCreateChildEvaluations($childId, $classId, $semester);

            $classGrade = ClassGrade::where('child_id', $childId)
                ->where('class_id', $classId)
                ->firstOrFail();

            $loader = app(ReportCardInputLoader::class);
            $engine = app(ReportCardEngine::class);
            $persister = app(ReportCardPersister::class);

            $input = $loader->load($classGrade, $semester);
            $calcResult = $engine->calculate($input);

            Config::set('report_card.auto_classification', $applyFinal);
            $saved = $persister->persist($classGrade, $semester, $calcResult, $input);

            return response()->json([
                'status' => 200,
                'message' => 'Tính toán lại học bạ thành công.',
                'data' => [
                    'evaluation_id' => $saved?->id,
                    'calculation_status' => $saved?->calculation_status?->value ?? $saved?->calculation_status,
                    'calculated_academic_performance' => $saved?->calculated_academic_performance,
                    'academic_performance' => $saved?->academic_performance?->value ?? $saved?->academic_performance,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('ajaxRecalculateReportCard error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi tính lại học bạ: ' . $e->getMessage(),
            ], 500);
        }
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
