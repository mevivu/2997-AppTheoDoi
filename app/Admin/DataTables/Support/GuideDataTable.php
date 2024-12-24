<?php

namespace App\Admin\DataTables\Support;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Support\SupportRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Support\SupportType;


class GuideDataTable extends BaseDataTable
{
    protected $nameTable = 'guideTable';


    public function __construct(
        SupportRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.support.datatable.action',
            'status' => 'admin.support.datatable.status',
            'title' => 'admin.support.datatable.title',
            'checkbox' => 'admin.common.checkbox'
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2];

        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => ActiveStatus::asSelectArray()
            ]
        ];

    }

    public function query()
    {
        return $this->repository->getByQueryBuilder([
            'type' => SupportType::Guide->value
        ]);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.support', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'title' => $this->view['title']
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['title', 'action', 'status', 'checkbox'];
    }
}