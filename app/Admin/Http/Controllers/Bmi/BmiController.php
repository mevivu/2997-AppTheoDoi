<?php

namespace App\Admin\Http\Controllers\Bmi;

use App\Admin\DataTables\Bmi\BmiDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Bmi\BmiRequest;
use App\Admin\Repositories\Bmi\BmiRepositoryInterface;
use App\Admin\Services\Bmi\BmiServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class BmiController extends Controller
{
    public function __construct(
        BmiRepositoryInterface $repository,
        BmiServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.bmi.index',
            'create' => 'admin.bmi.create',
            'edit' => 'admin.bmi.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.bmi.index',
            'create' => 'admin.bmi.create',
            'edit' => 'admin.bmi.edit',
            'delete' => 'admin.bmi.delete'
        ];
    }

    public function index(BmiDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Danh sách BMI tiêu chuẩn'),
            ]
        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'gender' => Gender::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Danh sách BMI tiêu chuẩn', route($this->route['index']))->add('Thêm mới'),
        ]);
    }

    public function store(BmiRequest $request): RedirectResponse
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
                'gender' => Gender::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Danh sách BMI tiêu chuẩn', route($this->route['index']))->add('Cập nhật'),
            ]
        );

    }

    public function update(BmiRequest $request): RedirectResponse
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