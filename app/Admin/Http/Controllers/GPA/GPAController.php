<?php

namespace App\Admin\Http\Controllers\GPA;

use App\Admin\DataTables\GPA\GPADataTable;
use App\Admin\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use App\Admin\Repositories\GPA\GPARepositoryInterface;
use App\Traits\ResponseController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Enums\ActiveStatus;
use App\Models\ClassGrade;

class GPAController extends Controller
{
    use ResponseController;

    public function __construct(
        GPARepositoryInterface $repository
    ) {

        parent::__construct();

        $this->repository = $repository;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.gpa.index',
            'status' => 'admin.gpa.datatable.status',
            'name' => 'admin.gpa.datatable.name',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.gpa.index',
        ];
    }
    public function index(GPADataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Danh sách GPA')),
            ]
        );
    }

    protected function getActionMultiple(): array
    {
        return [
            'active' => ActiveStatus::Active->description(),
            'draft' => ActiveStatus::Draft->description(),
            'deleted' => ActiveStatus::Deleted->description()
        ];
    }
}
