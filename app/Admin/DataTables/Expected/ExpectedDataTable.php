<?php

namespace App\Admin\DataTables\Expected;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Expected\ExpectedRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseType;
use App\Enums\User\Gender;


class ExpectedDataTable extends BaseDataTable
{
    protected $nameTable = 'expectedTable';


    public function __construct(
        ExpectedRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.expected.datatable.action',
            'status' => 'admin.expected.datatable.status',
            'checkbox' => 'admin.common.checkbox',
            'id' => 'admin.expected.datatable.id',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [2, 3, 4, 5];

        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => ActiveStatus::asSelectArray()
            ],
        ];

    }

    public function query()
    {
        return $this->repository->getQueryBuilderOrderBy();
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.expected', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'id' => $this->view['id'],
            'height_expected' => function ($row) {
                return $row->height_expected . ' cm';
            },
            'weight_expected' => function ($row) {
                return $row->weight_expected . ' kg';
            },
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
        $this->customRawColumns = ['id', 'action', 'status', 'checkbox'];
    }
}