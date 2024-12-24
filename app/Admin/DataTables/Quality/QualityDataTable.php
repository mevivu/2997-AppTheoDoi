<?php

namespace App\Admin\DataTables\Quality;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Quality\QualityRepositoryInterface;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

class QualityDataTable extends BaseDataTable
{
    protected $nameTable = 'qualityTable';


    public function __construct(
        QualityRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.quality.datatable.action',
            'status' => 'admin.quality.datatable.status',
            'name' => 'admin.quality.datatable.name',
            'checkbox' => 'admin.common.checkbox',
        ];
    }


    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3];
        $this->columnSearchDate = [3];
        $this->columnSearchSelect = [

            [
                'column' => 2,
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
        $this->customColumns = config('datatables_columns.qualities', []);
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            //
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'name' => $this->view['name'],
            'created_at' => function ($query) {
                return format_datetime($query->created_at);
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
        $this->customRawColumns = [
            'name',
            'status',
            'action',
            'checkbox',
        ];
    }


}