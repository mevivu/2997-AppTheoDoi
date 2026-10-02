<?php

namespace App\Admin\DataTables\Expert;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Expert\ExpertRepositoryInterface;
use App\Admin\Traits\GetConfig;
use App\Enums\DefaultStatus;

class ExpertDataTable extends BaseDataTable
{
    use GetConfig;

    protected $nameTable = 'ExpertTable';
    protected array $actions = ['reset', 'reload'];

    public function __construct(ExpertRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.experts.datatable.action',
            'avatar' => 'admin.experts.datatable.avatar',
            'council_type' => 'admin.experts.datatable.council_type',
            'editlink' => 'admin.experts.datatable.editlink',
            'is_verified' => 'admin.experts.datatable.is-verified',
            'status' => 'admin.experts.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [2, 3, 4, 5, 7];
        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => \App\Enums\Expert\ExpertCouncilType::asSelectArray(),
            ],
            [
                'column' => 7,
                'data' => [
                    DefaultStatus::Published->value => 'Hoạt động',
                    DefaultStatus::Draft->value => 'Tạm ẩn',
                ],
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderOrderBy('sort_order', 'asc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.expert', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'avatar' => $this->view['avatar'],
            'council_type' => $this->view['council_type'],
            'name' => $this->view['editlink'],
            'is_verified' => $this->view['is_verified'],
            'status' => $this->view['status'],
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['avatar', 'council_type', 'name', 'is_verified', 'status', 'action', 'checkbox'];
    }
}
