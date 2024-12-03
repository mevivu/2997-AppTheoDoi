<?php

namespace App\Admin\DataTables\Clinic;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Clinic\ClinicRepositoryInterface;
use App\Admin\Repositories\ClinicType\ClinicTypeRepositoryInterface;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

class ClinicDataTable extends BaseDataTable
{
    protected $nameTable = 'clinicTypeTable';

    protected array $actions = ['reset', 'reload'];


    public function __construct(
        ClinicRepositoryInterface $repository,
    ) {

        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.clinic.datatable.action',
            'name' => 'admin.clinic.datatable.name',
            'clinic_type_id' => 'admin.clinic.datatable.clinic_type_id',
            'status' => 'admin.clinic.datatable.status',
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
        $this->columnAllSearch = [1, 2, 3,4];
        $this->columnSearchDate = [4];
        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => ActiveStatus::asSelectArray()
            ],

        ];
//        $clinicTypeRepository = app(ClinicTypeRepositoryInterface::class);
//
//        $clinicTypes = $clinicTypeRepository->getAllClinicTypes()->map(function ($clinic) {
//            return [$clinic->id => $clinic->name];
//        });
//
//        $this->columnSearchSelect2 = [
//            [
//                'column' => 3,
//                'data' => $clinicTypes
//            ]
//        ];


    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.clinic', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'name' => $this->view['name'],
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'clinic_type_id' => function ($clinic) {
                return $clinic->clinicType->name?? 'N/A';
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
        $this->customRawColumns = ['action', 'name', 'status', 'checkbox'];
    }
    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'clinic_type_id' => function ($query, $keyword) {
                $query->whereHas('clinicType', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%');
                });
            },

        ];
    }

}
