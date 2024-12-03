<?php

namespace App\Admin\DataTables\VaccinationSchedule;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

class VaccinationScheduleDataTable extends BaseDataTable
{
    protected $nameTable = 'vaccinationScheduleTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        VaccinationScheduleRepositoryInterface $repository
    ) {

        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.vaccinationSchedule.datatable.action',
            'name' => 'admin.vaccinationSchedule.datatable.name',
            'status' => 'admin.vaccinationSchedule.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(
            [
                ['status', '!=', ActiveStatus::Deleted],
            ]
        );
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

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.vaccination_schedule', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'performed_on' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'name' => $this->view['name'],
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
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
        $this->customRawColumns = ['action', 'name', 'status', 'checkbox'];
    }
}
