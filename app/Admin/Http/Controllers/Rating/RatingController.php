<?php

namespace App\Admin\Http\Controllers\Rating;

use App\Admin\DataTables\Rating\AQ\RatingAQDatable;
use App\Admin\DataTables\Rating\EQ\RatingEQDatable;
use App\Admin\DataTables\Rating\IQ\RatingIQDatable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Rating\RatingRepositoryInterface;
use App\Admin\Services\Rating\RatingServiceInterface;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    use ResponseController;

    public function __construct(
        RatingRepositoryInterface $repository,
        RatingServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'eq' => 'admin.rating.eq',
            'aq' => 'admin.rating.aq',
            'iq' => 'admin.rating.iq',
        ];
    }

    public function getRoute(): array
    {
        return [
            'iq' => 'admin.rating.iq',
            'aq' => 'admin.rating.aq',
            'eq' => 'admin.rating.eq',
        ];
    }

    public function eq(RatingEQDatable $dataTable)
    {

        return $dataTable->render($this->view['eq'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add(__('đánh giá EQ'))
            ]
        );
    }

    public function iq(RatingIQDatable $dataTable)
    {

        return $dataTable->render($this->view['iq'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add(__('đánh giá IQ'))
            ]
        );
    }

    public function aq(RatingAQDatable $dataTable)
    {

        return $dataTable->render($this->view['aq'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add(__('đánh giá AQ'))
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
}
