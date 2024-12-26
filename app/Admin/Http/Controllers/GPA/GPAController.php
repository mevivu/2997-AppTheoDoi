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
        return $dataTable->render(
            $this->view['index'],
            [
                'breadcrumbs' => $this->crums->add(__('Danh sách GPA'))
            ]
        );
    }
}
