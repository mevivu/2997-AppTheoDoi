<?php

namespace App\Admin\Http\Controllers\Journal;

use App\Admin\DataTables\Journal\JournalMomentDataTable;
use App\Admin\DataTables\Journal\JournalPrescriptionDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Journal\JournalRequest;
use App\Admin\Repositories\Journal\JournalRepositoryInterface;
use App\Admin\Services\Journal\JournalServiceInterface;
use App\Enums\Journal\JournalType;
use App\Traits\ResponseController;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;


class JournalController extends Controller
{
    use ResponseController;

    public function __construct(
        JournalRepositoryInterface $repository,
        JournalServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'prescription' => 'admin.journals.prescription',
            'moment'=> 'admin.journals.moment',
            'create' => 'admin.journals.create',
            'edit' => 'admin.journals.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.journal.index',
            'create' => 'admin.journal.create',
            'edit' => 'admin.journal.edit',
            'delete' => 'admin.journal.delete',
            'moment' => 'admin.journal.moment',
            'prescription' => 'admin.journal.prescription',
        ];
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
                'type' => JournalType::asSelectArray(),
                'breadcrumbs' => $this->crums->add('DS nhật ký', route($this->route['prescription']))->add('Cập nhật'),
            ]
        );
    }
    public function moment(JournalMomentDataTable $datatable)
    {
        return $datatable->render(
            $this->view['moment'],
            [
                'type' => JournalType::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Danh sách nhật ký khoảnh khắc'),
            ]
        );
    }

    public function prescription(JournalPrescriptionDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['prescription'],
            [
                'type' => JournalType::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Hồ sơ y tế'),
            ]
        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'type' => JournalType::asSelectArray(),
            'breadcrumbs' => $this->crums->add('DS nhật ký',
                route($this->route['prescription']))->add('Thêm'),
        ]);
    }

    /**
     * @throws Exception
     */
    public function delete($id): RedirectResponse
    {

        $this->repository->delete($id);
        return redirect()->back()->with('success', __('notifySuccess'));

    }

    public function update(JournalRequest $request): RedirectResponse
    {
        $this->service->update($request);
        return back()->with('success', __('notifySuccess'));
    }

    public function store(JournalRequest $request): RedirectResponse
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['edit'], $response)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}
