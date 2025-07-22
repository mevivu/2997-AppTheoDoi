<?php

namespace App\Admin\Http\Controllers\Clinic;

use App\Admin\DataTables\Clinic\ClinicDataTable;
use App\Admin\Exel\Clinic\ClinicImport;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Clinic\ClinicImportRequest;
use App\Admin\Http\Requests\Clinic\ClinicRequest;
use App\Admin\Repositories\Clinic\ClinicRepositoryInterface;
use App\Admin\Services\Clinic\ClinicServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Date\DayOfWeek;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClinicController extends Controller
{
    use ResponseController;



    public function __construct(
        ClinicRepositoryInterface   $repository,
        ClinicServiceInterface      $service
    )
    {

        parent::__construct();

        $this->repository = $repository;
        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.clinic.index',
            'create' => 'admin.clinic.create',
            'edit' => 'admin.clinic.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.clinic.index',
            'create' => 'admin.clinic.create',
            'edit' => 'admin.clinic.edit',
            'delete' => 'admin.clinic.delete',
        ];
    }

    public function index(ClinicDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('clinic')),
            ]

        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'dayOfWeek' => DayOfWeek::cases(),
            'breadcrumbs' => $this->crums->add(__('clinic'),
                route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(ClinicRequest $request): RedirectResponse
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
        return view(
            $this->view['edit'],
            [
                'instance' => $instance,
                'dayOfWeek' => DayOfWeek::cases(),
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('DS Phòng khám'), route($this->route['index']))->add(__('edit')),
            ],
        );

    }

    public function update(ClinicRequest $request): RedirectResponse
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
            $response->delete();
            return true;
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

    public function export(): BinaryFileResponse
    {
        $filePath = public_path('assets/excel/clinic.xlsx');

        if (!file_exists($filePath)) {
            return abort(404);
        }

        return response()->download($filePath);
    }

    public function import(ClinicImportRequest $request): RedirectResponse
    {
        try {
            Excel::import(new ClinicImport(), $request->file('excelFile'));

            ClinicImport::afterImport();

            return back()->with('success', __('Import thành công!'));
        } catch (Exception $e) {
            $this->logError($e->getMessage(), $e);

            $message = $e->getMessage();

            $errorData = json_decode($message, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($errorData['type']) && $errorData['type'] === 'validation_errors') {
                return back()->with([
                    'import_errors' => $errorData['errors'],
                    'import_error_count' => $errorData['count']
                ]);
            } else {
                return back()->with('error', $message);
            }
        }
    }

    public function exportTemplate(): BinaryFileResponse
    {
        $filePath = public_path('assets/exel/TemplateImportClinic.xlsx');

        if (!file_exists($filePath)) {
            return abort(404);
        }

        return response()->download($filePath);
    }

}
