<?php

namespace App\Admin\DataTables\Pregnancy;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Pregnancy\PregnancyRepositoryInterface;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

class PregnancyDataTable extends BaseDataTable
{
    protected $nameTable = 'pregnancyTable';


    public function __construct(
        PregnancyRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'child' => 'admin.pregnancy.datatable.child',
            'action' => 'admin.pregnancy.datatable.action',
            'status' => 'admin.pregnancy.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }


    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3];

        $this->columnSearchDate = [2];

        $this->columnSearchSelect = [

            [
                'column' => 3,
                'data' => ActiveStatus::asSelectArray()
            ],
        ];
    }


    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(
            [
                ['status', '!=', ActiveStatus::Deleted],
            ]
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.pregnancy', []);
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'child_id' => function ($query, $keyword) {
                $query->whereHas('child', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', "%$keyword%");
                });
            },

        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'child_id' => function ($children) {
                return view($this->view['child'], [
                    'child' => $children->child,
                ])->render();
            },
            'start_date' => '{{ date("d-m-Y", strtotime($start_date)) }}',
            'end_date' => '{{ date("d-m-Y", strtotime($end_date)) }}',

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
        $this->customRawColumns = [
            'child_id',
            'status',
            'action',
            'checkbox',

        ];
    }


}
