<?php

namespace App\Admin\DataTables\ExpertCategory;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\ExpertCategory\ExpertCategoryRepositoryInterface;
use App\Admin\Traits\GetConfig;
use App\Enums\DefaultStatus;

class ExpertCategoryDataTable extends BaseDataTable
{
    use GetConfig;

    protected $nameTable = 'ExpertCategoryTable';
    protected array $actions = ['reset', 'reload'];

    public function __construct(ExpertCategoryRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.expert_categories.datatable.action',
            'editlink' => 'admin.expert_categories.datatable.editlink',
            'status' => 'admin.expert_categories.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3];
        $this->columnSearchSelect = [
            [
                'column' => 3,
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
        $this->customColumns = config('datatables_columns.expert_category', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'name' => $this->view['editlink'],
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
        $this->customRawColumns = ['name', 'status', 'action', 'checkbox'];
    }
}
