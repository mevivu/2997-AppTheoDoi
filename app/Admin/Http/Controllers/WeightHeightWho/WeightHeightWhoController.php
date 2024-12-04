<?php

namespace App\Admin\Http\Controllers\WeightHeightWho;

use App\Admin\DataTables\WeightHeightWho\WeightHeightWhoDatatable;
use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Http\Requests\WeightHeight\WeightHeightRequest;
use App\Admin\Repositories\Ward\WardRepositoryInterface;
use App\Admin\Http\Resources\Ward\WardSearchSelectResource;
use App\Admin\Repositories\WeightHeightWho\WeightHeightWhoRepositoryInterface;
use App\Admin\Services\WeightHeightWho\WeightHeightWhoServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WeightHeightWhoController extends BaseSearchSelectController
{
    public function __construct(WeightHeightWhoServiceInterface $service,WeightHeightWhoRepositoryInterface $repository)
    {
        parent::__construct();
        $this->service = $service;
        $this->repository = $repository;
    }
    public function getView():array
    {
        return [
            'create'=>'admin.WeightHeightWho.create',
            'index'=>'admin.WeightHeightWho.index',
            'edit'=>'admin.WeightHeightWho.edit',
        ];
    }
    public function getRoute():array
    {
        return [
            'index' => 'admin.weight-height-who.index',
            'create'=>'admin.weight-height-who.create',
            'edit'=>'admin.weight-height-who.edit',
            'delete'=>'admin.weight-height-who.delete',
        ];
    }
    public function edit($id):Factory|View|Application
    {
        $response=$this->repository->findOrFail($id);
        return view($this->view['edit'],
            [
                'response' => $response,
                'status' => ActiveStatus::asSelectArray(),
                'gender' => Gender::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Danh sách Cân nặng chiều cao theo chuẩn Who', route($this->route['index']))->add('Cập nhật'),
            ]);
    }
    public function index(WeightHeightWhoDatatable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Danh sách Cân nặng chiều cao tiêu chuẩn Who'),
            ]
        );
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
    public function create(): Factory|View|Application
    {
        return view($this->view['create'],[
            'status'=>ActiveStatus::asSelectArray(),
            'gender'=>Gender::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Danh sách Chiều cao cân nặng tiêu chuẩn', route($this->route['index']))->add('Thêm mới'),
        ]);

    }
    public function store(WeightHeightRequest $request): RedirectResponse
    {
        $response=$this->service->store($request);
        if($response){
            return to_route($this->route['edit'],$response)->with('success', __('notifySuccess'));
        }
        return  back()->with('error', __('notifyError'));
    }
    public function update(WeightHeightRequest $request): RedirectResponse
    {
        $this->service->update($request);

        return back()->with('success', __('notifySuccess'));
    }
    public function delete($id):RedirectResponse
    {
        $response=$this->repository->delete($id);

        return redirect()->back()->with('success', __('notifySuccess'));
    }
}
