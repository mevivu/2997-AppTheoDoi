<?php

namespace App\Admin\Http\Controllers\Journal;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Children\ChildrenRequest;
use App\Admin\Http\Requests\Journal\JournalRequest;
use App\Admin\Repositories\Journal\JournalRepositoryInterface;
use App\Admin\DataTables\Children\ChildrenDataTable;
use App\Admin\Services\Journal\JournalServiceInterface;
use App\Enums\Child\BornStatus;
use App\Enums\Journal\JournalType;
use App\Traits\ResponseController;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;


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
            'index' => 'admin.children.index',
            'create' => 'admin.journals.create',
            'edit' => 'admin.children.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.children.index',
            'create' => 'admin.journal.create',
            'edit' => 'admin.children.edit',
            'delete' => 'admin.children.delete',
        ];
    }


    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'type' => JournalType::asSelectArray(),
            'breadcrumbs' => $this->crums->add('DS nhật ký', route($this->route['index']))->add('Thêm'),
        ]);
    }
    public function store(JournalRequest $request)
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['create'], $response)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

}
