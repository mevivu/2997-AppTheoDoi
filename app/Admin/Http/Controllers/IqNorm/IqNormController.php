<?php

namespace App\Admin\Http\Controllers\IqNorm;

use App\Admin\DataTables\IqNorm\IqNormDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\IqNorm\IqNormRequest;
use App\Admin\Repositories\IqNorm\IqNormRepositoryInterface;
use App\Admin\Services\IqNorm\IqNormServiceInterface;
use App\Enums\ActiveStatus;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IqNormController extends Controller
{
    public function __construct(
        IqNormRepositoryInterface $repository,
        IqNormServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.iq_norms.index',
            'create' => 'admin.iq_norms.create',
            'edit' => 'admin.iq_norms.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.iq_norm.index',
            'create' => 'admin.iq_norm.create',
            'edit' => 'admin.iq_norm.edit',
            'delete' => 'admin.iq_norm.delete',
        ];
    }

    public function index(IqNormDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Bảng chuẩn IQ Wechsler theo độ tuổi'),
            ]
        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Bảng chuẩn IQ Wechsler theo độ tuổi', route($this->route['index']))->add('Thêm mới'),
        ]);
    }

    public function store(IqNormRequest $request): RedirectResponse
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['edit'], $response->id)->with('success', __('notifySuccess'));
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
                'breadcrumbs' => $this->crums->add('Bảng chuẩn IQ Wechsler theo độ tuổi', route($this->route['index']))->add('Cập nhật mốc tuổi ' . $response->age),
            ]
        );
    }

    public function update(IqNormRequest $request): RedirectResponse
    {
        $this->service->update($request);
        return back()->with('success', __('notifySuccess'));
    }

    public function delete($id): RedirectResponse
    {
        $this->repository->delete($id);
        return back()->with('success', __('notifySuccess'));
    }

    public function recalculate(): RedirectResponse
    {
        try {
            $summary = $this->service->recalculateFromRatings();
            $msg = "Tái chuẩn hóa thành công từ {$summary['total_ratings_processed']} bản ghi bài test IQ thực tế. Đã cập nhật {$summary['total_ages_updated']} mốc độ tuổi.";
            return back()->with('success', $msg);
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi khi tái chuẩn hóa: ' . $e->getMessage());
        }
    }

    public function actionMultipleRecords(Request $request): RedirectResponse
    {
        $result = $this->service->actionMultipleRecords($request);
        if ($result) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    protected function getActionMultiple(): array
    {
        return [
            ActiveStatus::Active->value => 'Kích hoạt',
            ActiveStatus::Draft->value => 'Bản nháp',
            ActiveStatus::Deleted->value => 'Tạm khóa',
        ];
    }
}
