<?php

namespace App\Admin\DataTables\VaccinationSchedule;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Permission\PermissionType;
use Illuminate\Database\Eloquent\Builder;

class AdminVaccinationScheduleDataTable extends BaseDataTable
{
    protected $nameTable = 'vaccinationScheduleTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        VaccinationScheduleRepositoryInterface $repository
    )
    {

        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.vaccinationSchedule.datatable.action',
            'name' => 'admin.vaccinationSchedule.datatable.name',
            'status' => 'admin.vaccinationSchedule.datatable.status',
            'vaccinationType' => 'admin.vaccinationSchedule.datatable.vaccinationType',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(['type' => PermissionType::ADMIN]);
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3, 4, 5];
        $this->columnSearchDate = [3];
        $this->columnSearchSelect = [
            [
                'column' => 4,
                'data' => ActiveStatus::asSelectArray()
            ],

        ];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.vaccination_schedule', []);
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'vaccination_type_id' => function ($query, $keyword) {
                $query->whereHas('vaccinationType', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', "%$keyword%");
                });
            },


        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'performed_on' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'name' => $this->view['name'],

            'vaccination_type_id' => function ($vaccinationType) {
                return view($this->view['vaccinationType'], [
                    'vaccinationType' => $vaccinationType->vaccinationType,
                ])->render();
            },
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
        $this->customRawColumns = ['action', 'vaccination_type_id', 'name', 'status', 'checkbox'];
    }
}
