<?php

namespace App\Admin\Http\Controllers\FetalGrowthStandard;

use App\Admin\DataTables\FetalGrowthStandard\FetalGrowthStandardDatatable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\FetalGrowthStandard\FetalGrowthStandardRequest;
use App\Admin\Repositories\FetalGrowthStandard\FetalGrowthStandardRepositoryInterface;
use App\Admin\Services\FetalGrowthStandard\FetalGrowthStandardServiceInterface;
use App\Admin\Exel\FetalGrowthStandard\FetalGrowthStandardTemplateExport;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FetalGrowthStandardController extends Controller
{
    use ResponseController;

    public function __construct(
        FetalGrowthStandardRepositoryInterface $repository,
        FetalGrowthStandardServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.fetal_growth_standards.index',
            'create' => 'admin.fetal_growth_standards.create',
            'edit' => 'admin.fetal_growth_standards.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.fetal-growth-standard.index',
            'create' => 'admin.fetal-growth-standard.create',
            'edit' => 'admin.fetal-growth-standard.edit',
            'delete' => 'admin.fetal-growth-standard.delete',
        ];
    }

    protected function getActionMultiple(): array
    {
        return [
            ActiveStatus::Active->value => ActiveStatus::Active->description(),
            ActiveStatus::Draft->value => ActiveStatus::Draft->description(),
            ActiveStatus::Deleted->value => ActiveStatus::Deleted->description(),
        ];
    }

    public function index(FetalGrowthStandardDatatable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'status' => ActiveStatus::asSelectArray(),
            'actionMultiple' => $this->getActionMultiple(),
            'breadcrumbs' => $this->crums->add(__('Tiêu chuẩn thai nhi theo tuần')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums
                ->add(__('Tiêu chuẩn thai nhi theo tuần'), route($this->route['index']))
                ->add(__('Thêm mới')),
        ]);
    }

    public function store(FetalGrowthStandardRequest $request)
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        });
    }

    public function edit($id): Factory|View|Application
    {
        $response = $this->repository->findOrFail($id);
        return view($this->view['edit'], [
            'response' => $response,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums
                ->add(__('Tiêu chuẩn thai nhi theo tuần'), route($this->route['index']))
                ->add(__('Cập nhật')),
        ]);
    }

    public function update(FetalGrowthStandardRequest $request)
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        $this->service->delete($id);
        return redirect()->back()->with('success', __('notifySuccess'));
    }

    public function actionMultipleRecords(Request $request): RedirectResponse
    {
        $response = $this->service->actionMultipleRecords($request);
        if ($response) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ], [
            'excel_file.required' => 'Vui lòng chọn file Excel để import',
            'excel_file.mimes' => 'File phải có định dạng .xlsx, .xls hoặc .csv',
        ]);

        try {
            Excel::import(new FetalGrowthStandardImport(), $request->file('excel_file'));
            return back()->with('success', 'Import tiêu chuẩn thai nhi thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(
            new FetalGrowthStandardTemplateExport(),
            'mau_nhap_tieu_chuan_thai_nhi.xlsx'
        );
    }
}

