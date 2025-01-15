<?php

namespace App\Admin\Http\Controllers\RatingPQ;

use App\Admin\DataTables\Pregnancy\PregnancyDataTable;
use App\Admin\DataTables\RatingPQ\RatingPQDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Pregnancy\PregnancyRequest;

use App\Admin\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Services\RatingPQ\RatingPQServiceInterface;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RatingPQController extends Controller
{
    use ResponseController;
    public function __construct(
        RatingPQRepositoryInterface $repository,
        RatingPQServiceInterface    $service
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
        'index' => 'admin.ratingPQ.index',
        'create' => 'admin.ratingPQ.create',
        'edit' => 'admin.ratingPQ.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.ratingPQ.index',
            'create' => 'admin.ratingPQ.create',
            'edit' => 'admin.ratingPQ.edit',
            'delete' => 'admin.ratingPQ.delete'
        ];
    }

    public function index(RatingPQDataTable $datatable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $datatable->render(
            $this->view['index'],
            [
                'actionMultiple' => $actionMultiple,
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Danh sách Thai kì'),
            ]
        );
    }

    public function update(PregnancyRequest $request)
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {

        $this->repository->delete($id);
        return redirect()->back()->with('success', __('notifySuccess'));

    }

    public function edit($id): Factory|View|Application
    {
        $response = $this->repository->findOrFail($id);
        return view($this->view['edit'], [
            'response' => $response,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('DS thai kì')->add('Cập nhật'),
        ]);
    }

    public function create(): Factory|View|Application
    {

        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Danh sách Thai kì', route($this->route['index']))->add('Thêm mới'),
        ]);
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
