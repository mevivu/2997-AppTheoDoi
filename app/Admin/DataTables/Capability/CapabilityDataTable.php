<?php

namespace App\Admin\DataTables\Capability;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Capability\CapabilityRepositoryInterface;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

class CapabilityDataTable extends BaseDataTable
{
    protected $nameTable = 'capabilityTable';


    public function __construct(
        CapabilityRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.capability.datatable.action',
            'status' => 'admin.capability.datatable.status',
            'name' => 'admin.capability.datatable.name',
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
        $this->customColumns = config('datatables_columns.capabilities', []);
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