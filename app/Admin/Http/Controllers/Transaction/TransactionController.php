<?php

namespace App\Admin\Http\Controllers\Transaction;

use App\Admin\DataTables\Transaction\TransactionDatable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Admin\Services\Transaction\TransactionServiceInterface;
use App\Traits\ResponseController;

class TransactionController extends Controller
{
    use ResponseController;

    public function __construct(
        TransactionRepositoryInterface $repository,
        TransactionServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.transaction.index',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.transaction.index',
        ];
    }

    public function index(TransactionDatable $dataTable)
    {
        return $dataTable->render($this->view['index'],
            [
                'breadcrumbs' => $this->crums->add(__('Danh sách giao dịch'))
            ]
        );
    }
}
