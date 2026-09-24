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

        $heightPrediction = null;
        $heightChart = null;
        try {
            $heightService = app(\App\Api\V1\Services\HeightPrediction\HeightPredictionServiceInterface::class);

            $heightRequest = new \App\Api\V2\Http\Requests\HeightPrediction\HeightPredictionV2Request();
            $heightRequest->setValidator(validator([
                'child_id' => $id,
                'puberty_months' => 0
            ], [
                'child_id' => 'required|numeric',
                'puberty_months' => 'required|numeric|min:0|max:96'
            ]));
            $heightPrediction = $heightService->indexV2($heightRequest);

            $chartRequest = new \App\Api\V2\Http\Requests\HeightPrediction\HeightChartV2Request();
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
        } catch (\Exception $e) {
            Log::warning('Error calculating height prediction/chart V2 for child ' . $id . ': ' . $e->getMessage());
        }

        $pqOverall = null;
        try {
            $pqService = app(\App\Api\V1\Services\RatingPQ\RatingPQServiceInterface::class);
            $pqOverall = $pqService->getOverallStats(new \Illuminate\Http\Request(['child_id' => $id]), (int)$id);
        } catch (\Exception $e) {
            Log::warning('Error calculating PQ overall for child ' . $id . ': ' . $e->getMessage());
        }

        return view(
            $this->view['edit'],
            [
                'children' => $instance,
                'heightPrediction' => $heightPrediction,
                'heightChart' => $heightChart,
                'pqOverall' => $pqOverall,
                'gender' => Gender::asSelectArray(),
                'birthday' => $instance->birthday,
                'dueDate' => $instance->due_date,
                'status' => ChildStatus::asSelectArray(),
                'born' => BornStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('childrenList'), route($this->route['index']))->add(__('edit')),
            ],
        );
    }

    public function ajaxPredictHeightV2(Request $request): \Illuminate\Http\JsonResponse
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

            $heightService = app(\App\Api\V1\Services\HeightPrediction\HeightPredictionServiceInterface::class);
            $serviceRequest = new \App\Api\V2\Http\Requests\HeightPrediction\HeightPredictionV2Request();
            $serviceRequest->setValidator($validator);

            $result = $heightService->indexV2($serviceRequest);

            return response()->json([
                'status' => 200,
                'message' => 'Tính toán dự báo chiều cao V2 thành công.',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('ajaxPredictHeightV2 error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi tính toán dự báo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ajaxHeightChartV2(Request $request): \Illuminate\Http\JsonResponse
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

            $heightService = app(\App\Api\V1\Services\HeightPrediction\HeightPredictionServiceInterface::class);
            $serviceRequest = new \App\Api\V2\Http\Requests\HeightPrediction\HeightChartV2Request();
            $serviceRequest->setValidator($validator);

            $result = $heightService->chartV2($serviceRequest);

            return response()->json([
                'status' => 200,
                'message' => 'Lấy dữ liệu phác đồ chiều cao V2 thành công.',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('ajaxHeightChartV2 error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi tải phác đồ: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function ajaxDebugHeightV2(Request $request): \Illuminate\Http\JsonResponse
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

            $heightService = app(\App\Api\V1\Services\HeightPrediction\HeightPredictionServiceInterface::class);
            $debugData = $heightService->debugHeightRegimen((int)$childId, $pubertyMonths, $targetHeight);

            return response()->json([
                'status' => 200,
                'message' => 'Lấy dữ liệu chẩn đoán công thức thành công.',
                'data' => $debugData,
            ]);
        } catch (\Exception $e) {
            Log::error('ajaxDebugHeightV2 error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi khi lấy dữ liệu chẩn đoán: ' . $e->getMessage(),
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
