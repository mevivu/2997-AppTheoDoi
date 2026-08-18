<?php

namespace App\Admin\Http\Controllers\RatingPQ;

use App\Admin\DataTables\RatingPQ\RatingPQDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Admin\Services\RatingPQ\RatingPQServiceInterface;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Exception;
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
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.ratingPQ.index',
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
                'breadcrumbs' => $this->crums->add('Danh sách Đánh giá'),
            ]
        );
    }


    /**
     * @throws Exception
     */
    public function delete($id): RedirectResponse
    {

        $this->repository->delete($id);
        return redirect()->back()->with('success', __('notifySuccess'));

    }


    protected function getActionMultiple(): array
    {
        return [
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
